import { ref } from 'vue';

const toasts = ref([]);

export function useToast() {
    const dismiss = (id) => {
        toasts.value = toasts.value.filter(t => t.id !== id);
    };

    const show = ({ name, task }) => {
        const id = Date.now();
        toasts.value.push({ id, name, initial: name.charAt(0), task });
        setTimeout(() => dismiss(id), 4000);
    };

    return { toasts, show, dismiss };
}
