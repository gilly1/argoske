import { createApp } from 'vue';
require('./bootstrap');

let app=createApp({})
app.component('fields', require('./components/Fields.vue').default);
app.component('approvers', require('./components/Approvers.vue').default);
app.component('task-draggable', require('./components/Draggable.vue').default);
app.component('gits', require('./components/gits.vue').default);
app.component('gits-commit', require('./components/gits_commit.vue').default);
app.component('items-edit', require('./components/ItemEdit.vue').default);
app.component('items', require('./components/Item.vue').default);
app.mount("#app")

