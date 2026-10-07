<?php

namespace App\Http\Controllers;

use App\Service\BundleCategoryAndListResolutionService;
use App\Service\FlutterwaveService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Domains\Bills\Airtime\Services\RetrieveAirtimeProviders;
use App\Domains\Bills\Data\Services\RetrieveDataBundlePageInfo;
use App\Mail\ComplaintSubmitted;
use App\Models\Beneficiary;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Domains\Transactions\Services\RetrieveWalletBalance;
use App\Transactions\RetrieveTransactionFee;
use App\Models\TransactionFeeSetting;

class UserController extends Controller
{
    public function __construct(
        protected FlutterwaveService $flutterwaveService,
        protected RetrieveAirtimeProviders $retrieveAirtimeProviders,
        protected RetrieveDataBundlePageInfo $retrieveDataBundlePageInfo,
        protected RetrieveWalletBalance $retrieveWalletBalance,
        protected RetrieveTransactionFee $retrieveTransactionFee,
    ) {}

    public function indexPage(Request $request)
    {
        return Inertia::render('app/MainIndex', [
            'balanceShowImage' => Storage::disk('public')->url('show.png'),
            'balanceHideImage' => Storage::disk('public')->url('hide.png'),
            'clients_name' => Str::before(Auth::user()->name ?? 'avalon the goat', ' '),
            'balance' => $request->user()->account->walletBalance(),
            'recentTransactions' => Inertia::scroll(fn() => Transaction::cursorPaginate())
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('index-page');
        }

        return back()->withErrors([
            'email' => 'The provided credentials are incorrect.',
        ])->onlyInput('email');
    }

    public function transferPage(Request $request)
    {
        $search = $request->input('search');

        $beneficiaries = Beneficiary::query()
            ->where('type', 'transfer')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('recipient_name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('transaction_reference', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('app/MainTransfer', [
            'beneficiaries' => Inertia::scroll(
                fn() => $beneficiaries
            ),
        ]);
    }

    public function transferAmountPage()
    {
        return Inertia::render('app/AmountToTransfer');
    }
    public function cardInfo()
    {
        return Inertia::render(
            'app/cardInfo',
            [
                'account_number' => Auth::user()->account->account_number,
                'account_name' => Auth::user()->account->account_name,
                'bank_name' => Auth::user()->account->bank_name,
                'clients_name' => Str::before(Auth::user()->name ?? 'avalon the goat', ' '),
            ]
        );
    }

    public function pin()
    {
        return Inertia::render('app/Pin');
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4',
        ]);

        $user = Auth::user();

        if ($request->input('pin') === $user->pin) {
            $accessToken = Crypt::encryptString($user->pin);
            $token_created = now();
            session(['access_token' => $accessToken, 'token_created' => $token_created]);

            // extract the intended URL from the session and redirect to it
            return Http::post(route('complete_transfer'));  //  for test purposes
        } else {
            return response()->json(['status' => 'error', 'message' => 'Invalid PIN'], 401);
        }
    }

    public function transactionStatus(Request $request)
    {
        return Inertia::render('app/TransferStatus', [
            'transactionReference' => $request->input('transactionReference'),
            'transaction' => Transaction::where('transaction_reference', $request->input('transactionReference'))->first(),
            'clients_name' => $request->input('clients_name'),
        ]);
    }

    public function otpPage(Request $request)
    {
        return Inertia::render('app/Otp');
    }

    public function registerPage()
    {
        return Inertia::render('app/Register');
    }

    public function loginPage()
    {
        return Inertia::render('app/Login');
    }

    public function clientProfilePage()
    {
        return Inertia::render('app/ClientProfile', [
            'first_name' => Auth::user()->first_name,
            'middle_name' => Auth::user()->middle_name,
            'last_name' => Auth::user()->last_name,
            'email' => Auth::user()->email,
            'account_status' => Auth::user()->account->status,
            'account_number' => Auth::user()->account->account_number,
            'account_name' => Auth::user()->account->account_name,
            'bank_name' => Auth::user()->account->bank_name,
        ]);
    }

    public function confirmationPage()
    {
        return Inertia::render('app/Confirmation');
    }

    public function completeTransferPage()
    {
        return Inertia::render('app/CompleteTransfer', [
            'available_balance' => $this->retrieveWalletBalance->calculateAvailableBalance(),
        ]);
    }

    public function clientProfilePageEdit()
    {
        return Inertia::render('app/EditProfile', [
            'first_name' => Auth::user()->first_name,
            'middle_name' => Auth::user()->middle_name,
            'last_name' => Auth::user()->last_name,
        ]);
    }
    public function clientProfileEdit(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'string', 'min:8'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (isset($validated['password'])) {
            if (Hash::check($validated['current_password'], $user->password)) {
                $user->first_name = $validated['first_name'];
                $user->middle_name = $validated['middle_name'] ?? null;
                $user->last_name = $validated['last_name'];
                $user->email = $validated['email'];

                // Automatically update full name
                $user->full_name = trim(
                    $validated['first_name'] . ' ' .
                        ($validated['middle_name'] ?? '') . ' ' .
                        $validated['last_name']
                );

                // Only update password if one was supplied
                if (!empty($validated['password'])) {
                    $user->password = Hash::make($validated['password']);
                }

                $user->save();

                return back()->with('success', 'Profile updated successfully.');
            } else {
                return back()->with('error', 'something went wront try again later.');
            }
        }

        $user->first_name = $validated['first_name'];
        $user->middle_name = $validated['middle_name'] ?? null;
        $user->last_name = $validated['last_name'];

        // Automatically update full name
        $user->full_name = trim(
            $validated['first_name'] . ' ' .
                ($validated['middle_name'] ?? '') . ' ' .
                $validated['last_name']
        );
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
    public function submitComplaint(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Mail::to(config('mail.admin_mail'))
            ->queue(new ComplaintSubmitted(
                message: $validated['message']
            ));

        return back()->with('success', 'Complaint submitted successfully.');
    }

    public function airtimePage()
    {
        $provider_list = $this->retrieveAirtimeProviders->excecute();
        if ($provider_list) {
            Log::info('airtime providers list', $provider_list->toArray());
        }

        return Inertia::render('app/Airtime', [
            'airtime_providers' => $provider_list,
        ]);
    }

    public function dataPage()
    {
        $data_bundle_page_info = $this->retrieveDataBundlePageInfo->execute();
        return Inertia::render('app/Data', [
            'bundle_list' => $data_bundle_page_info['data_map']->toArray(),
            'bundle_category' => $data_bundle_page_info['data_provider_categories'],
            'bundle_providers' => $data_bundle_page_info['data_providers_list']->toArray(),
        ]);
    }

    public function dataBundles(Request $request)
    {
        $dataProvider = $request->input('selectedProviderValue');
        Log::info($dataProvider);
        $data_bundle_page_info = $this->retrieveDataBundlePageInfo->execute($dataProvider);
        Log::info($data_bundle_page_info);
        return response()->json([
            'bundle_list' => $data_bundle_page_info['data_map']->toArray(),
            'bundle_category' => $data_bundle_page_info['data_provider_categories'],
        ]);
    }

    public function history(Request $request)
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'statuses' => ['nullable', 'array'],
            'statuses.*' => ['string'],
        ]);

        $transactions = Transaction::query()
            ->forUser($request->user()->id)
            ->category($validated['category'] ?? null)
            ->dateRange(
                $validated['startDate'] ?? null,
                $validated['endDate'] ?? null
            )
            ->statuses($validated['statuses'] ?? null)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->cursorPaginate(20)
            ->withQueryString();

        return Inertia::render('app/History', [
            'history' => Inertia::scroll(
                fn() => $transactions
            ),
        ]);
    }

    public function paymentPinPage(Request $request)
    {
        return Inertia::render('app/PaymentPin');
    }

    public function transactionDetail(Request $request)
    {
        $transactionReference = $request->query('reference');

        $transaction = Transaction::where(
            'transaction_reference',
            $transactionReference
        )->firstOrFail();
        return Inertia::render('app/TransactionDetail', [
            'transactionDetail' => $transaction,
            'completedAtHuman' => $transaction->completed_at?->diffForHumans(),
        ]);
    }

    public function helpDesk(Request $request)
    {
        return Inertia::render('app/HelpDesk');
    }

    public function transactionFee(Request $request)
    {
        $amount = $request->input('amount');
        $flutterwaveFee = $this->retrieveTransactionFee->execute(amount: $amount);
        $applicationFee = TransactionFeeSetting::calculateFee($amount);
        return response()->json(['fee' => (int)$flutterwaveFee + (int)$applicationFee]);
    }
}
