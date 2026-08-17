import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { AuthProvider, useAuth, Role } from "@/context/AuthContext";
import { Spinner } from "@/components/ui";
import DashboardLayout from "@/components/DashboardLayout";
import { navFor } from "@/lib/nav";
import { roleHome } from "@/lib/roles";

// Marketing
import Home from "@/pages/marketing/Home";
import Features from "@/pages/marketing/Features";
import Pricing from "@/pages/marketing/Pricing";
import FAQ from "@/pages/marketing/FAQ";
import Contact from "@/pages/marketing/Contact";
import StartTrial from "@/pages/StartTrial";
import Login from "@/pages/Login";

// School dashboard
import AdminDashboard from "@/pages/dashboard/AdminDashboard";
import StudentDashboard from "@/pages/dashboard/StudentDashboard";
import Results from "@/pages/dashboard/Results";
import POS from "@/pages/dashboard/POS";
import Settings from "@/pages/dashboard/Settings";
import {
  StudentsPage, TeachersPage, ClassesPage, SubjectsPage, AnnouncementsPage,
  FeesPage, InventoryPage, ExamsPage, ExpensesPage,
} from "@/pages/dashboard/resources";

// Platform
import PlatformDashboard from "@/pages/platform/PlatformDashboard";
import Schools from "@/pages/platform/Schools";
import Plans from "@/pages/platform/Plans";
import Subscriptions from "@/pages/platform/Subscriptions";

function Protected({ roles, children }: { roles: Role[]; children: React.ReactNode }) {
  const { user, loading } = useAuth();
  if (loading) return <div className="flex min-h-screen items-center justify-center"><Spinner /></div>;
  if (!user) return <Navigate to="/login" replace />;
  if (!roles.includes(user.role)) return <Navigate to={roleHome(user.role)} replace />;
  return <>{children}</>;
}

function AppShell({ children }: { children: React.ReactNode }) {
  const { user } = useAuth();
  return <DashboardLayout nav={navFor(user!.role)}>{children}</DashboardLayout>;
}

const SCHOOL_STAFF: Role[] = ["school_admin", "teacher", "staff", "sales_staff"];

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          {/* Public marketing */}
          <Route path="/" element={<Home />} />
          <Route path="/features" element={<Features />} />
          <Route path="/pricing" element={<Pricing />} />
          <Route path="/faq" element={<FAQ />} />
          <Route path="/contact" element={<Contact />} />
          <Route path="/start" element={<StartTrial />} />
          <Route path="/login" element={<Login />} />

          {/* School app */}
          <Route path="/app" element={<Protected roles={SCHOOL_STAFF}><AppShell><AdminDashboard /></AppShell></Protected>} />
          <Route path="/app/students" element={<Protected roles={SCHOOL_STAFF}><AppShell><StudentsPage /></AppShell></Protected>} />
          <Route path="/app/teachers" element={<Protected roles={["school_admin"]}><AppShell><TeachersPage /></AppShell></Protected>} />
          <Route path="/app/classes" element={<Protected roles={SCHOOL_STAFF}><AppShell><ClassesPage /></AppShell></Protected>} />
          <Route path="/app/subjects" element={<Protected roles={SCHOOL_STAFF}><AppShell><SubjectsPage /></AppShell></Protected>} />
          <Route path="/app/results" element={<Protected roles={SCHOOL_STAFF}><AppShell><Results /></AppShell></Protected>} />
          <Route path="/app/exams" element={<Protected roles={SCHOOL_STAFF}><AppShell><ExamsPage /></AppShell></Protected>} />
          <Route path="/app/announcements" element={<Protected roles={SCHOOL_STAFF}><AppShell><AnnouncementsPage /></AppShell></Protected>} />
          <Route path="/app/fees" element={<Protected roles={["school_admin"]}><AppShell><FeesPage /></AppShell></Protected>} />
          <Route path="/app/accounting" element={<Protected roles={["school_admin"]}><AppShell><ExpensesPage /></AppShell></Protected>} />
          <Route path="/app/inventory" element={<Protected roles={["school_admin", "sales_staff"]}><AppShell><InventoryPage /></AppShell></Protected>} />
          <Route path="/app/pos" element={<Protected roles={["school_admin", "sales_staff"]}><AppShell><POS /></AppShell></Protected>} />
          <Route path="/app/settings" element={<Protected roles={["school_admin"]}><AppShell><Settings /></AppShell></Protected>} />

          {/* Student portal */}
          <Route path="/app/student" element={<Protected roles={["student", "parent"]}><AppShell><StudentDashboard /></AppShell></Protected>} />

          {/* Platform / super admin */}
          <Route path="/platform" element={<Protected roles={["super_admin"]}><AppShell><PlatformDashboard /></AppShell></Protected>} />
          <Route path="/platform/schools" element={<Protected roles={["super_admin"]}><AppShell><Schools /></AppShell></Protected>} />
          <Route path="/platform/plans" element={<Protected roles={["super_admin"]}><AppShell><Plans /></AppShell></Protected>} />
          <Route path="/platform/subscriptions" element={<Protected roles={["super_admin"]}><AppShell><Subscriptions /></AppShell></Protected>} />

          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}
