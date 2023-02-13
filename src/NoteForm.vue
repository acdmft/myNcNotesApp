<template>
  
    <div v-if="$store.state.currentNote.active" class="input-wrapper">
      <input
        ref="title"
        v-model="$store.state.currentNote.title"
        type="text"
        :disabled="updating"
        placeholder="title"
      />
      <textarea
        ref="content"
        v-model="$store.state.currentNote.content"
        :disabled="updating"
        placeholder="note content"
      />
      <input
        type="button"
        class="primary"
        :value="t('mynotesapp', 'Save')"
        :disabled="updating || !savePossible"
        @click="saveNote"
      />
      <p>{{ currNote }}</p>
    </div>
    <div v-else id="emptycontent">
      <div class="icon-file" />
      <h2>{{ t("mynotesapp", "Hello! You can create new note!") }}</h2>
    </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { showError, showSuccess } from "@nextcloud/dialogs";
import { generateUrl } from "@nextcloud/router";
export default {
  name: "NoteForm",
  data() {
    return {
      updating: false,
      savePossible: true,
      // currentNote: null,
    };
  },
  props: {
    // currentNote: null,
    // currentNote: null,
  },
  computed: {
    currNote() {
      return `${this.$store.state.currentNote.title} ${this.$store.state.currentNote.content}`
    }
  },

  async mounted() {
    console.log('mounted')
    this.$nextTick(() => {
          this.$refs.title.focus();
        });
  },
  methods: {
    openNote(note) {
      if (this.updating) {
        return;
      }
      this.$store.state.currentNote.id = note.id;
      this.$nextTick(() => {
        this.$refs.content.focus();
      });
    },
    
    saveNote() {
      // crée l'objet note
      // const note = { note: "valeur", .. }
      // sauvegarde la note dans la liste note qui est partagé entre les composants => Vuex
      // this.store.notes[] = note
      if (this.$store.state.currentNote.id === -1) {
        this.createNote(this.$store.state.currentNote);
      } else {
        this.updateNote(this.$store.state.currentNote);
      }
      
    },
    async createNote(note) {
      this.updating = true;
      try {
        const response = await axios.post(
          generateUrl("/apps/mynotesapp/notes"),
          {
            title: this.$store.state.currentNote.title,
             content: this.$store.state.currentNote.content}
        );
        const index = this.$store.state.notes.findIndex(
          (match) => match.id === this.$store.state.currentNote.id
        );
        this.$set(this.$store.state.notes, index, response.data);
        this.$store.state.currentNote.id = response.data.id;
      } catch (e) {
        console.error(e);
        showError(t("notestutorial", "Could not create the note"));
      }
      this.updating = false;
    },
    async updateNote(note) {
      this.updating = true;
      try {
        await axios.put(generateUrl(`/apps/mynotesapp/notes/${note.id}`), note);
      } catch (e) {
        console.error(e);
        showError(t("notestutorial", "Could not update the note"));
      }
      this.updating = false;
    },
  },
};
</script>

<style>
.input-wrapper {
  display: flex;
  width: 100%;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}
input[type="text"],
input[type="button"] {
  width: 70%;
  margin: 1rem 0;
}
input[type="button"] {
  width: 30%;
}

textarea {
  flex-grow: 1;
  width: 70%;
  margin: 1rem 0;
}
</style>
