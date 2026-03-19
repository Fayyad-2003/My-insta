import { Comment } from "../common/comment";
import { Like } from "../common/like";
import { Paginated } from "../common/paginated";
import { User } from "../user/user";
import { ReelSettings } from "./reel-settings";

export type Reel = {
  id: number;
  slug: string;
  user: User;
  caption: string | null;
  uri: string;
  thumbnail?: string | null;
  music?: string | null;
  tags?: string[] | null;
  aiLabels?: string[] | null;
  isPublished?: boolean;
  location?: string | null;
  createdAt?: string;
  updatedAt?: string;

  likesCount?: number;
  commentsCount?: number;
  sharesCount?: number;
  viewsCount?: number;

  likes: Paginated<Like>;
  comments: Paginated<Comment>;

  isLiked?: boolean;
  isBookmarked?: boolean;

  settings?: ReelSettings | null;
  authCanManage?: boolean;
  authCanLike?: boolean;
  authBookmark?: boolean;
};
