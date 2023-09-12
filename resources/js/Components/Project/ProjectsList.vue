<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue';

const props = defineProps({ project: Object, currentUser: Object })

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
  window.Echo.private('tasks.' + props.project.id).listen('TaskCreated', e => {
    reloadCurrentPage();
  }).listenForWhisper('typing', e => {
    activePeer.name = e.name;

    if (typingTimer.value) clearTimeout(typingTimer.value)

    typingTimer.value = setTimeout(() => {
      activePeer.name = '';
    }, 3000)
  });
})

let activePeer = reactive({ name: '' });
let typingTimer = ref(false);

function tapParticipants() {
  window.Echo.private('tasks.' + props.project.id)
    .whisper('typing', {
      name: props.currentUser.name
    });
}

</script>


<template>
  <div>
    <ul>
      <li v-for="task in project['tasks']" v-text="task['body']"></li>
    </ul>

    <label for="task_name">Task name</label>
    <input id="task_name" type="text" v-model="form.body" @blur="submit" @keydown="tapParticipants">
    <span v-if="activePeer.name && activePeer.name.length > 0" v-text="activePeer.name + ' is typing...'"></span>
  </div>
</template>