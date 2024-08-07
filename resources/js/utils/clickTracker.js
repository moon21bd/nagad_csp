import axios from "axios";

const trackClick = (
    event,
    additionalData,
    subjectType = null,
    subjectId = null
) => {
    axios
        .post("/click-activity", {
            event: event,
            additional_data: additionalData || null,
            subject_type: subjectType,
            subject_id: subjectId,
        })
        .then((response) => {
            console.log("Click tracked successfully:", response.data);
        })
        .catch((error) => {
            console.error("Error tracking click:", error);
        });
};

export default trackClick;
