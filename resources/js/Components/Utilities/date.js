import { formatDistance, parseISO } from "date-fns";

export const formatDate = (date) => formatDistance(parseISO(date), new Date());
