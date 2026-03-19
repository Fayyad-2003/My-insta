import { User } from "../user/user";

export interface Like {
  id: number;
  user: User;
  timestamp: string;
}
