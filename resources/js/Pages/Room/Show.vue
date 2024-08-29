<script setup>
import Footer from "@/Components/Chat/Footer.vue";
import Header from "@/Components/Chat/Header.vue";
import Messages from "@/Components/Chat/Messages.vue";
import Nav from "@/Components/Chat/Nav.vue";
import { useMessagesStore } from "@/Store/useMessagesStore";
import { useUsersStore } from "@/Store/useUsersStore";
import { Head } from "@inertiajs/vue3";
import { onUnmounted, ref } from "vue";

const props = defineProps({
    room: {
        type: Object,
        required: true,
    },
});

onUnmounted(() => {
    Echo.leave(`room.${props.room.id}`);
});

const messagesStore = useMessagesStore();
const usersStore = useUsersStore();
const mobileSidebarOpen = ref(false);

const handleSidebar = (value) => {
    mobileSidebarOpen.value = value;
};

// mobileSidebarOpen.value = true;

const storeMessage = (payload) => {
    messagesStore.storeMessage(props.room.slug, payload);
};

const channel = Echo.join(`room.${props.room.id}`);

channel
    .listen("MessageCreated", (e) => {
        messagesStore.pushMessage(e);
    })
    .here((users) => usersStore.setUsers(users))
    .joining((user) => usersStore.addUser(user))
    .leaving((user) => usersStore.removeUser(user))
    .listenForWhisper("typing", (e) => {
        usersStore.setTyping(e);
    });

messagesStore.fetchMessages(props.room.slug);
</script>

<template>
    <Head title="Messages" />
    <div>
        <!-- Page Container -->
        <div
            id="page-container"
            class="relative mx-auto h-screen min-w-[320px] bg-white lg:ms-80"
        >
            <!-- Page Sidebar -->
            <Nav
                :mobile-sidebar-open="mobileSidebarOpen"
                :handle-sidebar="handleSidebar"
            />
            <!-- Page Sidebar -->

            <!-- Page Header -->
            <Header
                :mobile-sidebar-open="mobileSidebarOpen"
                :handle-sidebar="handleSidebar"
            />
            <!-- END Page Header -->

            <!-- Page Content -->
            <Messages :room="room" />
            <!-- END Page Content -->

            <!-- Page Footer -->
            <Footer
                v-on:valid="storeMessage({ content: $event })"
                v-on:typing="
                    channel.whisper('typing', {
                        user_id: $page.props.auth.user.id,
                        typing: $event,
                    })
                "
            />
            <!-- END Page Footer -->
        </div>
        <!-- END Page Container -->
    </div>
</template>
