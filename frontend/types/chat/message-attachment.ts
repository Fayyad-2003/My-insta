export interface MessageAttachment {
  id: number;
  message_id: number;
  file_path: string;
  type: string;
  size?: number | null;
  metadata?: MediaMetadata | null;
  file_type?: string | null;
  createdAt?: string;
  updatedAt?: string;
}
