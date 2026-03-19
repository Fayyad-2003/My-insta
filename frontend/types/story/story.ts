import { Media } from "../common/media";
import { Paginated } from "../common/paginated";
import { User } from "../user/user";
import { StorySettings } from "./strory-settings";

export type Story = {
  id: number;
  slug: string;
  type: string;
  user: User;
  content?: string | null;
  expiresAt?: string | null;
  location?: string | null;
  createdAt: string;
  media: Media;
  viewsCount?: number;
  sharesCount?: number;
  hasViewed: boolean;
  authCanManage?: boolean;
  setting?: StorySettings | null;
  viewers?: Paginated<User>;
};
