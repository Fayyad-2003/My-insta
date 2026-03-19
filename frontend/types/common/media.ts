export interface Media {
  id?: number;
  uri: string;
  type: "image" | "video";
  order?: number;
  thumbnailUri?: string;
  mime?: string;
  metadata?: Record<string, any>;
}

export interface Video {
  id: number;
  uri: string;
  thumbnail: string | null;
}
