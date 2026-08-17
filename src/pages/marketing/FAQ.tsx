import MarketingLayout from "@/components/MarketingLayout";

const faqs = [
  ["Is my school's data isolated from others?", "Yes. SAS is fully multi-tenant — every record is tagged with your school_id and users can never access another school's information."],
  ["Can I use my own school branding?", "Absolutely. School Admins can change the school name, logo, favicon, colors, contact details, and even receipt and report-card branding."],
  ["How does the free trial work?", "New schools start on a configurable trial (7, 14 or 30 days). You get full access and can upgrade, downgrade or move to the Free plan at any time."],
  ["What exams are supported?", "CBT, class tests, mock, practice, internal and entrance examinations — with MCQ, true/false, multiple-answer and short-answer questions, plus automatic grading."],
  ["Which payment providers do you support?", "Payments can be processed via Flutterwave, Paystack or Stripe. Payment success is verified through secure server-side webhooks."],
  ["Can teachers and students have their own logins?", "Yes. There are dedicated experiences for Super Admin, School Admin, Teacher/Staff, Student, Parent and Sales Staff, each with role-based permissions."],
];

export default function FAQ() {
  return (
    <MarketingLayout>
      <div className="mx-auto max-w-3xl px-4 py-16">
        <h1 className="text-center text-4xl font-extrabold text-slate-900">Frequently asked questions</h1>
        <div className="mt-10 space-y-4">
          {faqs.map(([q, a]) => (
            <details key={q} className="card group p-5">
              <summary className="flex cursor-pointer list-none items-center justify-between font-semibold text-slate-900">
                {q}
                <span className="text-brand-600 transition group-open:rotate-45">＋</span>
              </summary>
              <p className="mt-3 text-sm text-slate-600">{a}</p>
            </details>
          ))}
        </div>
      </div>
    </MarketingLayout>
  );
}
