// =====================
// USER

import { Paginated } from "../common/paginated";
import { Contact } from "./contact";

// =====================
export interface User {
  id: number;
  slug: string;
  name: string;
  username?: string;
  email: string;
  avatar?: string;
  avatarUrl?: string | null;
  bio?: string | null;
  location?: string | null;
  isVerified?: boolean;
  isFollowing?: boolean;
  isFollowedBy?: boolean;
  followersCount?: number;
  followingCount?: number;
  postsCount?: number;
  lastActiveAt?: string | null;
  activeStory?: boolean;
  createdAt: string;

  category?: string;
  contact?: Contact;
  settings?: Record<string, any> | null;
  authCanFollow?: boolean;
  authCanMessage?: boolean;

  friends?: Paginated<User>;
  followers?: Paginated<User>;
  following?: Paginated<User>;
}
