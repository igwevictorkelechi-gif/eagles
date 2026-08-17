import type { NavLink } from "@/components/DashboardLayout";
import type { Role } from "@/context/AuthContext";

export const adminNav: NavLink[] = [
  { to: "/app", label: "Dashboard", icon: "🏠" },
  { to: "/app/students", label: "Students", icon: "🎓" },
  { to: "/app/teachers", label: "Teachers", icon: "👩‍🏫" },
  { to: "/app/classes", label: "Classes", icon: "🏫" },
  { to: "/app/subjects", label: "Subjects", icon: "📚" },
  { to: "/app/results", label: "Results", icon: "📝" },
  { to: "/app/exams", label: "Examinations", icon: "🖥️" },
  { to: "/app/announcements", label: "Announcements", icon: "📢" },
  { to: "/app/fees", label: "Fees", icon: "💳" },
  { to: "/app/accounting", label: "Accounting", icon: "📒" },
  { to: "/app/inventory", label: "Inventory", icon: "📦" },
  { to: "/app/pos", label: "POS / Sales", icon: "🛒" },
  { to: "/app/settings", label: "Settings", icon: "⚙️" },
];

export const teacherNav: NavLink[] = [
  { to: "/app", label: "Dashboard", icon: "🏠" },
  { to: "/app/students", label: "Students", icon: "🎓" },
  { to: "/app/classes", label: "Classes", icon: "🏫" },
  { to: "/app/results", label: "Results", icon: "📝" },
  { to: "/app/exams", label: "Examinations", icon: "🖥️" },
  { to: "/app/announcements", label: "Announcements", icon: "📢" },
];

export const salesNav: NavLink[] = [
  { to: "/app/pos", label: "POS", icon: "🛒" },
  { to: "/app/inventory", label: "Products", icon: "📦" },
];

export const studentNav: NavLink[] = [
  { to: "/app/student", label: "Dashboard", icon: "🏠" },
];

export const platformNav: NavLink[] = [
  { to: "/platform", label: "Overview", icon: "🏠" },
  { to: "/platform/schools", label: "Schools", icon: "🏫" },
  { to: "/platform/plans", label: "Plans", icon: "🏷️" },
  { to: "/platform/subscriptions", label: "Subscriptions", icon: "💠" },
];

export function navFor(role: Role): NavLink[] {
  switch (role) {
    case "super_admin": return platformNav;
    case "school_admin": return adminNav;
    case "teacher": case "staff": return teacherNav;
    case "sales_staff": return salesNav;
    case "student": case "parent": return studentNav;
    default: return adminNav;
  }
}
