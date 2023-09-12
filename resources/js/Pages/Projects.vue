<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ProjectsList from '@/Components/Project/ProjectsList.vue';

defineProps({ project: Object, currentUser: Object })
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Project
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                  <ProjectsList :project="project" :current-user="currentUser"></ProjectsList>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
