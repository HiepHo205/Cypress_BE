export function roleColor(name) {
  switch (name) {
    case "admin":
      return "bg-red-500";

    case "user":
      return "bg-blue-500";

    default:
      return "bg-gray-500";
  }
}