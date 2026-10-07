import * as z from "zod";
import { emailSchema } from "./email";
import { passwordSchema } from "./password";

export const phaseOne = z.object({
  email: emailSchema,
  password: passwordSchema,
});
