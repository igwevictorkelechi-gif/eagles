import { createContext, useContext, useEffect, useState, ReactNode } from "react";
import { api, getToken, setToken } from "@/lib/api";

export type Role =
  | "super_admin" | "school_admin" | "teacher" | "staff" | "student" | "parent" | "sales_staff";

export interface User {
  id: string;
  role: Role;
  school_id: string | null;
  name: string;
  email?: string;
}
export interface School {
  id: string;
  name: string;
  slug: string;
  logo_url?: string | null;
  primary_color?: string | null;
  secondary_color?: string | null;
}

interface AuthState {
  user: User | null;
  school: School | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<User>;
  register: (payload: Record<string, unknown>) => Promise<User>;
  logout: () => void;
  refresh: () => Promise<void>;
}

const Ctx = createContext<AuthState>(null as any);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [school, setSchool] = useState<School | null>(null);
  const [loading, setLoading] = useState(true);

  function applyBrand(s: School | null) {
    if (s?.primary_color) document.documentElement.style.setProperty("--brand", s.primary_color);
  }

  async function refresh() {
    if (!getToken()) { setLoading(false); return; }
    try {
      const res = await api.get<{ user: User; school: School | null }>("/auth/me");
      setUser(res.user);
      setSchool(res.school);
      applyBrand(res.school);
    } catch {
      setUser(null);
      setSchool(null);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => { refresh(); }, []);

  async function login(email: string, password: string) {
    const res = await api.post<{ token: string; user: User; school: School | null }>("/auth/login", { email, password });
    setToken(res.token);
    setUser(res.user);
    setSchool(res.school);
    applyBrand(res.school);
    return res.user;
  }

  async function register(payload: Record<string, unknown>) {
    const res = await api.post<{ token: string; user: User; school: School | null }>("/auth/register", payload);
    setToken(res.token);
    setUser(res.user);
    setSchool((res.school as any) ?? null);
    return res.user;
  }

  function logout() {
    setToken(null);
    setUser(null);
    setSchool(null);
  }

  return (
    <Ctx.Provider value={{ user, school, loading, login, register, logout, refresh }}>
      {children}
    </Ctx.Provider>
  );
}

export const useAuth = () => useContext(Ctx);
