export function uid(prefix = ""): string {
  return prefix + crypto.randomUUID();
}

export function slugify(input: string): string {
  return input
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "")
    .slice(0, 48);
}

export function gradeFor(score: number): { grade: string; remark: string } {
  if (score >= 75) return { grade: "A", remark: "Excellent" };
  if (score >= 65) return { grade: "B", remark: "Very Good" };
  if (score >= 55) return { grade: "C", remark: "Good" };
  if (score >= 45) return { grade: "D", remark: "Pass" };
  if (score >= 40) return { grade: "E", remark: "Weak Pass" };
  return { grade: "F", remark: "Fail" };
}
