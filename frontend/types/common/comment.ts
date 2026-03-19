import { Post } from "../post/post";
import { Reel } from "../reel/reel";
import { User } from "../user/user";

export type Comment = {
  id: number;
  user: User;
  text?: string | null;
  body?: string | null;
  parentId?: number | null;
  timestampt: string;
  likesCount?: number;
  isLiked?: boolean;
  post?: Post | Reel;
  // likes?: Paginated<Like>;
  // replies?: Paginated<Comment>;
  createdAt: string;
};
