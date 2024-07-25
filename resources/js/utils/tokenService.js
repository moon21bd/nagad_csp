import axios from "../axios";
import store from "../store";

const renewToken = async () => {
    try {
        const response = await axios.post("/api/renew-token");
        const { token } = response.data;
        store.commit("auth/setToken", token);
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    } catch (error) {
        console.error("Token renewal failed:", error);
    }
};

export default renewToken;
