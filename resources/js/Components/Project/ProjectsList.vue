<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue';

const props = defineProps({ project: Object })

const form = reactive({
  body: ''
})

function submit() {
  if (form.body === '') {
    alert('Value must be filled.');

    return;
  }

  router.post(`/api/projects/${props.project.id}/tasks`, form);

  form.body = '';
}

function reloadCurrentPage() {
  // Using Inertia's `visit` method to reload the current page
  router.reload({
    only: ['project'],
  })

  // router.visit(window.location.href, {
  //   only: ['projects']
  // })

}

onMounted(() => {
  // window.Echo.channel('tasks.' + props.project.id ).listen('TaskCreated', e => {
  //   reloadCurrentPage();
  // });
  window.Echo.private('tasks.' + props.project.id).listen('TaskCreated', e => {
    reloadCurrentPage();
  });
})

</script>


<template>
  <div>
    <ul>
      <li v-for="task in project['tasks']" v-text="task['body']"></li>
    </ul>

    <label for="task_name">Task name</label>
    <input id="task_name" type="text" v-model="form.body" @blur="submit">
  </div>
</template>