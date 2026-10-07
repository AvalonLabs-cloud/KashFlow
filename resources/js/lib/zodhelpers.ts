import { z } from "zod";

/**
 * SHARED RULES
 * Extensible constants for easier maintenance and consistency.
 */
const PASSWORD_REGEX = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

const NAME_RULES = z
  .string()
  .trim()
  .min(2, "Must be at least 2 characters")
  .max(50, "Must not exceed 50 characters");

/**
 * SCHEMA DEFINITION
 */
export const UserRegistrationSchema = z
  .object({
    firstname: NAME_RULES,
    lastname: NAME_RULES,
    email: z
      .email("Invalid email format")
      .toLowerCase()
      .trim(),
    password: z
      .string()
      .min(8, "Password must be at least 8 characters")
      .regex(
        PASSWORD_REGEX,
        "Password must contain uppercase, lowercase, number, and special character"
      ),
    passwordConfirmation: z.string(),
  })
  .strict() // Prevents unexpected fields (security best practice)
  .refine((data) => data.password === data.passwordConfirmation, {
    message: "Passwords do not match",
    path: ["passwordConfirmation"], // Maps error specifically to this field
  });

/**
 * TYPES
 * Automatically inferred from the schema to ensure a single source of truth.
 */
export type UserRegistrationInput = z.infer<typeof UserRegistrationSchema>;

/**
 * HELPER UTILITIES
 */

// Throws error on failure - Use for internal service logic
export const validateUser = (data: unknown): UserRegistrationInput => {
  return UserRegistrationSchema.parse(data);
};

// Returns a Result object - Use for API controllers/Form handling
export const safeParseUser = (data: unknown) => {
  return UserRegistrationSchema.safeParse(data);
};

// Formats Zod errors into a clean, flat key-value object
export const getValidationErrors = (error: z.ZodError) => {
    return z.treeifyError(error)
;
};
