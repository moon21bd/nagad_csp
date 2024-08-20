module.exports = {
    apps: [
        {
            name: "raqib-worker-test",
            script: "artisan",
            args: "queue:work --queue=default",
            cwd: "/var/raqib",
            interpreter: "php",
            exec_mode: "fork",
            instances: 3,
            autorestart: true,
            watch: false,
            max_memory_restart: "1G",
            env: {
                APP_ENV: "staging",
            },
        },
    ],
};
