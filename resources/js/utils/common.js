import { format, formatDistanceToNow, parseISO } from "date-fns";

/**
 * Capitalizes the first letter of a string.
 * @param {string} str - The string to capitalize.
 * @returns {string} - The capitalized string.
 */
export function capitalize(str) {
    if (typeof str !== "string") return "";
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Human readable time format generator.
 * @param {date} date - Any date string.
 * @returns {string} - The formatted distance to now.
 */
export const timeAgo = (date) => {
    return formatDistanceToNow(parseISO(date), { addSuffix: true });
};

/**
 * Human readable time format generator.
 * @param {date} date - Any date string.
 * @returns {string} - The formatted distance to now.
 */
export const formatDateTime = (date) => {
    return format(parseISO(date), "MMM d, yyyy 'at' h:mm a");
};
