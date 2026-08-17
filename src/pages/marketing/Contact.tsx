import { useState } from "react";
import MarketingLayout from "@/components/MarketingLayout";
import { Field } from "@/components/ui";

export default function Contact() {
  const [sent, setSent] = useState(false);
  return (
    <MarketingLayout>
      <div className="mx-auto max-w-2xl px-4 py-16">
        <div className="text-center">
          <h1 className="text-4xl font-extrabold text-slate-900">Get in touch</h1>
          <p className="mt-3 text-slate-500">Questions about SAS? Send us a message and we'll get back to you.</p>
        </div>
        {sent ? (
          <div className="card mt-10 p-8 text-center">
            <div className="text-4xl">✅</div>
            <p className="mt-3 font-medium text-slate-700">Thanks! Your message has been received.</p>
          </div>
        ) : (
          <form
            className="card mt-10 space-y-4 p-6"
            onSubmit={(e) => { e.preventDefault(); setSent(true); }}
          >
            <div className="grid gap-4 sm:grid-cols-2">
              <Field label="Full name"><input required className="input" placeholder="Jane Doe" /></Field>
              <Field label="Email"><input required type="email" className="input" placeholder="jane@school.edu" /></Field>
            </div>
            <Field label="School name"><input className="input" placeholder="Greenfield Academy" /></Field>
            <Field label="Message"><textarea required rows={4} className="input" placeholder="How can we help?" /></Field>
            <button type="submit" className="btn-primary w-full">Send message</button>
          </form>
        )}
        <div className="mt-8 grid gap-4 text-center text-sm text-slate-500 sm:grid-cols-3">
          <div className="card p-4"><p className="font-semibold text-slate-700">Email</p><p>hello@sas.app</p></div>
          <div className="card p-4"><p className="font-semibold text-slate-700">Phone</p><p>+234 800 000 0000</p></div>
          <div className="card p-4"><p className="font-semibold text-slate-700">Hours</p><p>Mon–Fri, 9am–5pm</p></div>
        </div>
      </div>
    </MarketingLayout>
  );
}
