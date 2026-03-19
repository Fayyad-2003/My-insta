import { Paginated } from "../common/paginated";
import { Post } from "../post/post";
import { Reel } from "../reel/reel";
import { User } from "../user/user";

export type FeedItem =
  | { type: "users"; data: User[] }
  | { type: "post"; data: Post }
  | { type: "reel"; data: Reel }
  | { type: "suggestedPosts"; data: Post[] }
  | { type: "suggestedReels"; data: Reel[] };

export type HomeFeed = Paginated<FeedItem>;
