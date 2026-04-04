import { HttpService } from "../../commons/services/HttpService.ts";
import type { CustomSession } from "../../login/contexts/SessionContext.ts";

const API_BASE_URL_V1 = import.meta.env.VITE_API_BASE_URL_V1;

const urlBase64ToUint8Array = (base64String: string): Uint8Array<ArrayBuffer> => {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
    const raw = atob(base64);
    const buffer = new ArrayBuffer(raw.length);
    const output = new Uint8Array(buffer);
    for (let i = 0; i < raw.length; ++i) {
        output[i] = raw.charCodeAt(i);
    }
    return output;
};

const getVapidPublicKey = async (): Promise<string> => {
    const response = await fetch(`${API_BASE_URL_V1}/push-vapid-key`);
    const data = await response.json();
    return data.publicKey;
};

const registerServiceWorker = async (): Promise<ServiceWorkerRegistration | null> => {
    if (!("serviceWorker" in navigator)) return null;
    try {
        return await navigator.serviceWorker.register("/sw.js");
    } catch {
        return null;
    }
};

const subscribeToPush = async (
    registration: ServiceWorkerRegistration,
    vapidPublicKey: string
): Promise<PushSubscription | null> => {
    try {
        const existing = await registration.pushManager.getSubscription();
        if (existing) return existing;

        return await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
        });
    } catch {
        return null;
    }
};

const sendSubscriptionToServer = async (
    subscription: PushSubscription,
    userId: string,
    session: CustomSession | null
): Promise<void> => {
    const keys = subscription.toJSON().keys;
    await HttpService.apiv1(
        {
            endpoint: `/push-subscription/${userId}`,
            method: "PUT",
            body: {
                endpoint: subscription.endpoint,
                authKey: keys?.auth ?? "",
                p256dhKey: keys?.p256dh ?? "",
            },
        },
        session
    );
};

export const PushNotificationService = {
    init: async (session: CustomSession | null): Promise<void> => {
        if (!session?.id || !("Notification" in window)) return;

        const permission = await Notification.requestPermission();
        if (permission !== "granted") return;

        const vapidKey = await getVapidPublicKey();
        if (!vapidKey) return;

        const registration = await registerServiceWorker();
        if (!registration) return;

        const subscription = await subscribeToPush(registration, vapidKey);
        if (!subscription) return;

        await sendSubscriptionToServer(subscription, session.id, session);
    },

    unsubscribe: async (session: CustomSession | null): Promise<void> => {
        if (!session?.id) return;
        try {
            const registration = await navigator.serviceWorker.getRegistration();
            const subscription = await registration?.pushManager.getSubscription();
            if (subscription) await subscription.unsubscribe();

            await HttpService.apiv1(
                { endpoint: `/push-subscription/${session.id}`, method: "DELETE" },
                session
            );
        } catch {
            // best-effort cleanup
        }
    },
};
