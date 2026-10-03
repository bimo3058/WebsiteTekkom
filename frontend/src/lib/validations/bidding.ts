import { z } from "zod"

export const createBidSchema = z.object({
  title_id: z.string().min(1, "Please select a title"),
})

export type CreateBidFormData = z.infer<typeof createBidSchema>
