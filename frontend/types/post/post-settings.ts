export interface PostSettings {
  allowComments?: boolean;
  allowShare?: boolean;
  allowReactions?: boolean;
  visibility?: "public" | "private" | "friends";
  postType?: "standard" | "reel" | "story";
  isPinned?: boolean;
  allowTagging?: boolean;
  allowMentions?: boolean;
  captionColor?: string | null;
  captionSize?: string | null;
}
