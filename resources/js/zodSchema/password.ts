import { z } from "zod";
export const passwordSchema = z.string().superRefine((password, ctx) => {
  if (password.length < 8) {
    ctx.addIssue({
        code: 'custom',
        message: "Password must be at least 8 characters long",
    });
  }

  if (!/[A-Z]/.test(password)) {
    ctx.addIssue({
      code: 'custom',
      message: "no alphabet character",
    });
  }

  if (!/[0-9]/.test(password)) {
    ctx.addIssue({
      code: 'custom',
      message: "no number",
    });
  }

  if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) {
    ctx.addIssue({
      code: 'custom',
      message: "no special character",
    });
  }
});
