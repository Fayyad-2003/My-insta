import { User } from "../user/user";

export interface Notification {
  id: number;
  type: string;
  message?: string | null;
  data: Record<string, any>;
  isRead: boolean;
  createdAt: string;
  sender: User;
}
