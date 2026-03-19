export interface MessageMetadata {
  story_id?: number;
  post_id?: number;
  reel_id?: number;

  preview?: {
    thumbnail?: string | null;
    type: "story" | "post" | "reel";
  };

  reaction?: boolean;
  reply_to_message_id?: number;
  [key: string]: any;
}
