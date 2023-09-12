<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue';

defineProps({ tasks: Object })

const form = reactive({
  body: ''
})

function submit() {
  router.post('tasks', form);

  form.body = '';
}

function reloadCurrentPage() {
  // Using Inertia's `visit` method to reload the current page
  router.reload({
    only: ['tasks'],
  })

  // router.visit(window.location.href, {
  //   only: ['tasks']
  // })

}

onMounted(() => {
  window.Echo.channel('tasks').listen('TaskCreated', e => {
    reloadCurrentPage();
  });
})

</script>


<template>
  <div>
    <ul>
      <li v-for="task in tasks" v-text="task"></li>
    </ul>

    <input type="text" v-model="form.body" @blur="submit">
  </div>
</template>