export function phaseOneValidator() {
    const errors = {
        email: '',
        password: [],
        confirmPassword: '',
    };

    const validated = {
        email: false,
        password: false,
        confirmPassword: false,
    };

    function validateEmail(email) {
        if (!email) {
            errors.email = 'Email is required';
            validated.email = false;

            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(email)) {
            errors.email = 'Please enter a valid email address';
            validated.email = false;

            return;
        }

        errors.email = '';
        validated.email = true;
    }

    function validatePassword(password) {
        const passwordErrors = [];

        if (!password) {
            passwordErrors.push('Password is required');
        } else {
            if (password.length < 8) {
                passwordErrors.push('Password must be at least 8 characters');
            }

            if (!/[A-Z]/.test(password)) {
                passwordErrors.push(
                    'Password must contain at least one uppercase letter',
                );
            }

            if (!/[a-z]/.test(password)) {
                passwordErrors.push(
                    'Password must contain at least one lowercase letter',
                );
            }

            if (!/[0-9]/.test(password)) {
                passwordErrors.push(
                    'Password must contain at least one number',
                );
            }
        }

        errors.password = passwordErrors;
        validated.password = passwordErrors.length === 0;
    }

    function validateConfirmPassword(password, confirmation) {
        if (!confirmation) {
            errors.confirmPassword = 'Please confirm your password';
            validated.confirmPassword = false;

            return;
        }

        if (password !== confirmation) {
            errors.confirmPassword = 'Passwords do not match';
            validated.confirmPassword = false;

            return;
        }

        errors.confirmPassword = '';
        validated.confirmPassword = true;
    }

    function isValid() {
        return (
            validated.email && validated.password && validated.confirmPassword
        );
    }

    return {
        errors,
        validated,
        validateEmail,
        validatePassword,
        validateConfirmPassword,
        isValid,
    };
}
