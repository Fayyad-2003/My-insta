import { User } from "../user/user";
import { MessageAttachment } from "./message-attachment";
import { MessageReaction } from "./message-reaction";
import { ParticipantMessage } from "./participant-message";

export interface Message {
  id: number;
  conversationId: number;
  isMe: boolean;
  senderId: number;
  type: "text" | "image" | "video" | "system" | "other";
  content?: string | null;
  metadata?: Record<string, any> | null;
  replyToId?: number | null;
  deletedForEveryoneAt?: string | null;
  createdAt: string;
  updatedAt: string;
  status: "sending" | "sent" | "failed";

  // Relationships
  sender?: User;
  attachments?: MessageAttachment[];
  reactions?: MessageReaction[];
  deliveries?: ParticipantMessage[];
  replyTo?: Message | null;
}
