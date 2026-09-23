export interface Lecturer {
  id: number;
  name: string;
}

export interface Title {
  id: number;
  title: string;
  description: string;
  quota: number;
  lecturer: Lecturer;
}

export interface Bid {
  id: number;
  title_id: number;
  priority: number;
  status: string;
  lecturer_recommendation: string | null;
  proposed_supervisor_1_id: number | null;
  proposed_supervisor_2_id: number | null;
  proposed_supervisor1: Lecturer | null;
  proposed_supervisor2: Lecturer | null;
  title: Title;
}

export interface GroupInfo {
  id: number;
  is_solo?: boolean;
  members: { id: number; student: { id: number }; is_leader: boolean }[];
  period?: { min_group_size?: number; max_group_size?: number };
}

export interface ProposalItem {
  id: number;
  title: string;
  description: string;
  supervisor_approval_status: 'PENDING' | 'UNDER_REVIEW' | 'APPROVED' | 'REJECTED' | string;
  proposed_supervisor?: Lecturer | null;
}

export interface BiddingFlow {
  can_submit_bid: boolean;
  can_reorder_bid: boolean;
  can_delete_bid: boolean;
  reason: string | null;
}
