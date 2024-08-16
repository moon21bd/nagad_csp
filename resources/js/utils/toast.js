import { Message } from "element-ui";

const ToastPlugin = {
    install(Vue) {
        Vue.prototype.$showToast = (message, options = {}) => {
            Message({
                message: message,
                type: options.type || "info",
                duration: options.duration || 3000,
                position: options.position || "top-right",
                ...options,
            });
        };
    },
};

export default ToastPlugin;
