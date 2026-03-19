import { User } from "../user/user";

export interface ConversationParticipant {
  id: number;
  conversationId: number;
  role: "owner" | "member";
  createdAt: string;
  updatedAt: string;
  user: User;
}
