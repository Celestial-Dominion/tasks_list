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
  window.Echo.join('tasks.' + props.project.id)
    .here(users => {
      participants.items = [...users]
    })
    .joining(user => {
      participants.items.push(user)
    })
    .leaving(user => {
      participants.items.splice(participants.items.indexOf(user), 1);
    })
    .listen('TaskCreated', e => {
      reloadCurrentPage();
    })
    .listenForWhisper('typing', e => {
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

let participants = reactive({
  items: []
});

</script>


<template>
  <div class="bg-gray-100 py-10">
    <div class="max-w-4xl mx-auto flex space-x-6">
      <!-- Task List and Completed Tasks-->
      <div class="flex-grow space-y-6">
        <!-- Task List -->
        <div class="flex-grow bg-white shadow-md rounded-lg p-5 space-y-4">
          <!-- Input field and submit button for adding tasks -->
          <div class="flex space-x-4">
            <!-- Input Field -->
            <input type="text" v-model="form.body" @keydown="tapParticipants" placeholder="Add a new task..."
              class="form-input flex-grow py-2 px-4 rounded-md shadow-sm">

            <!-- Submit Button -->
            <button @click="submit" class="py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add
              Task</button>
          </div>
          <!-- Typing Indicator -->
          <span v-if="activePeer.name && activePeer.name.length > 0" class="text-gray-500 text-sm mt-2"
            v-text="activePeer + ' is typing ...'"></span>

          <!-- Task Item -->
          <div class="flex items-center space-x-4 mt-4" v-for="task in project['tasks']" :key="project['tasks']['id']">
            <!-- Checkbox -->
            <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">

            <!-- Task Name -->
            <span class="text-gray-800 font-medium flex-grow min-w-0 truncate">{{ task['body'] }}</span>

            <!-- Button Container -->
            <div class="flex-shrink-0 flex space-x-2">
              <!-- Edit Button -->
              <button class="text-blue-500 hover:underline">Edit</button>

              <!-- Delete Button -->
              <button class="text-red-500 hover:underline">Delete</button>
            </div>
          </div>
        </div>
        <!-- Completed Tasks List -->
        <div class="bg-green-50 shadow-md rounded-lg p-5 space-y-4">
          <h3 class="text-lg font-bold mb-4 text-green-700">Completed Tasks</h3>

          <!-- Sample Completed Task -->
          <div class="flex items-center space-x-4">
            <!-- Checkbox (Checked) -->
            <input type="checkbox" class="form-checkbox h-5 w-5 text-green-600" checked>

            <!-- Completed Task Name -->
            <span class="text-gray-600 line-through">Sample Completed Task</span>
          </div>

          <!-- You can duplicate the above block for more completed tasks -->

        </div>
      </div>

      <!-- Online Users List -->
      <div class="w-1/4 h-48 bg-white shadow-md rounded-lg p-5 overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Active Participants</h3>
        <ul class="space-y-2">
          <li class="flex items-center space-x-2" v-for="(participant, index) in participants.items" :key="index">
            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
            <span>{{ participant.name }}</span>
          </li>
          <!-- Add more users as needed -->
        </ul>
      </div>
    </div>
  </div>
</template>