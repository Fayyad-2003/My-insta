import { Like } from "../common/like";
import { Media, Video } from "../common/media";
import { Paginated } from "../common/paginated";
import { User } from "../user/user";
import { PostSettings } from "./post-settings";

export type Post = {
  id: number;
  slug: string;
  user: User;
  content: string;
  media?: Media[];
  videoData?: Video | null;
  location?: string | null;
  createdAt?: string;
  updatedAt?: string;

  likes: Paginated<Like>;
  comments: Paginated<Comment>;

  likesCount?: number;
  commentsCount?: number;
  sharesCount?: number;
  viewsCount?: number;

  isLiked?: boolean;
  isBookmarked?: boolean;

  settings?: PostSettings | null;
  authCanManage?: boolean;
  authCanLike?: boolean;
};
