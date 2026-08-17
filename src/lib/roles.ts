import type { Role } from "@/context/AuthContext";

export function roleHome(role: Role): string {
  switch (role) {
    case "super_admin": return "/platform";
    case "student": return "/app/student";
    case "sales_staff": return "/app/pos";
    default: return "/app";
  }
}

export const roleLabel: Record<Role, string> = {
  super_admin: "Super Admin",
  school_admin: "School Admin",
  teacher: "Teacher",
  staff: "Staff",
  student: "Student",
  parent: "Parent",
  sales_staff: "Sales Staff",
};
