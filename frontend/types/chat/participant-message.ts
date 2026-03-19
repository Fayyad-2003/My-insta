export interface ParticipantMessage {
  id: number;
  messageId: number;
  participantId: number;
  deliveredAt?: string | null;
  readAt?: string | null;
  deletedForMeAt?: string | null;
  pinnedAt?: string | null;
}
