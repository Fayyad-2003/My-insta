import { User } from "../user/user";
import { ConversationParticipant } from "./conversation-participant";
import { Message } from "./message";

export interface Conversation {
  id: number;
  type: "direct" | "group";
  title?: string | null;
  image?: string | null;
  createdBy: number;
  createdAt: string;
  updatedAt: string;
  deletedAt?: string | null;
  otherUser?: User | null;
  participants: ConversationParticipant[];
  latestMessage: Message | null;
}
