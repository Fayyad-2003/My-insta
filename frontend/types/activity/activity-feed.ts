import { Comment, Post, Reel, Story, User } from "../index";

export type Activity =
  | "post_like"
  | "post_comment"
  | "post"
  | "reel"
  | "reel_like"
  | "reel_comment";

export interface ActivityObject {
  id: number;
  type:
    | "like"
    | "comment"
    | "post"
    | "reel"
    | "reel_comment"
    | "post_comment"
    | "reel_like";
  post?: Post;
  reel?: Reel;
  comment?: Comment;
  user?: User;
  story?: Story;
}
