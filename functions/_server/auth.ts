import { sign, verify } from "hono/jwt";
import type { MiddlewareHandler } from "hono";
import type { Env, JwtPayload, Role, Variables } from "./types";

const WEEK = 60 * 60 * 24 * 7;

export async function issueToken(
  secret: string,
  payload: Omit<JwtPayload, "exp">,
): Promise<string> {
  const exp = Math.floor(Date.now() / 1000) + WEEK;
  return sign({ ...payload, exp }, secret, "HS256");
}

// Require a valid JWT. Attaches `user` to context.
export const requireAuth: MiddlewareHandler<{
  Bindings: Env;
  Variables: Variables;
}> = async (c, next) => {
  const header = c.req.header("Authorization") || "";
  const token = header.startsWith("Bearer ") ? header.slice(7) : null;
  if (!token) return c.json({ error: "Unauthorized" }, 401);
  try {
    const payload = (await verify(token, c.env.JWT_SECRET, "HS256")) as unknown as JwtPayload;
    c.set("user", payload);
    await next();
  } catch {
    return c.json({ error: "Invalid or expired session" }, 401);
  }
};

// Require the authenticated user to hold one of the given roles.
export function requireRole(...roles: Role[]): MiddlewareHandler<{
  Bindings: Env;
  Variables: Variables;
}> {
  return async (c, next) => {
    const user = c.get("user");
    if (!user || !roles.includes(user.role)) {
      return c.json({ error: "Forbidden" }, 403);
    }
    await next();
  };
}

// The tenant a request operates on. Super admins have no school_id.
export function schoolId(c: { get: (k: "user") => JwtPayload }): string {
  const user = c.get("user");
  if (!user.school_id) throw new Error("No tenant context");
  return user.school_id;
}
