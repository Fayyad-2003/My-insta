import { User } from "../user/user";

export interface MessageReaction {
  id: number;
  messageId: number;
  userId: number;
  reaction: string;
  createdAt: string;

  user?: User;
}
