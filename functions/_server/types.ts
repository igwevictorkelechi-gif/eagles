export interface Env {
  DB: D1Database;
  JWT_SECRET: string;
}

export type Role =
  | "super_admin"
  | "school_admin"
  | "teacher"
  | "staff"
  | "student"
  | "parent"
  | "sales_staff";

export interface JwtPayload {
  sub: string; // user id
  role: Role;
  school_id: string | null;
  name: string;
  exp: number;
  [key: string]: unknown;
}

// Hono context variables
export type Variables = {
  user: JwtPayload;
};
