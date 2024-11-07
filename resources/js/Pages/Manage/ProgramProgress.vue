<template>
  <inertia-head title="Dashboard" />

  <ProgramLayout :competition="competition">
    <a-page-header title="Competition Progress" class="bg-white">
      <template #extra>
        <span class="">already choose {{ select_bouts.length }} bouts</span>
        <a-button
          type="text"
          class="text-blue-500"
          @click="() => ((this.select_bouts = []), (this.select_bout = {}))"
          >Clear</a-button
        >
        <a-button type="text" class="text-blue-500" @click="confirmPrint(competition)"
          >Show</a-button
        >
      </template>
    </a-page-header>
    <template v-if="competition.status >= 2">
      <div class="flex pt-4">
        <div class="h-[90vh] resizable overflow-x-hidden">
          <div class="bg-white px-2">
            <a-radio-group
              v-model:value="currentSection.id"
              button-style="solid"
              @change="onSectionChange()"
            >
              <template v-for="section in competition.section_number" :key="section.id">
                <a-radio-button :value="section">{{ section }}</a-radio-button>
              </template>
            </a-radio-group>
            <div class="grid gap-6" :class="'grid-cols-' + competition.mat_number">
              <div v-for="mat in competition.mat_number" :key="mat">
                <p>
                  Section: {{ currentSection.mats[mat].program.section }} / Mat:
                  {{ currentSection.mats[mat].program.mat }}
                </p>
                <a-button
                  @click="
                    currentSection.mats[mat].sequence--;
                    onMatChange();
                  "
                  >-</a-button
                >
                <a-input
                  type="input"
                  v-model:value="currentSection.mats[mat].sequence"
                  @change="onMatChange()"
                  style="width: 80px; text-align: center"
                />
                <a-button
                  @click="
                    currentSection.mats[mat].sequence++;
                    onMatChange();
                  "
                  >+</a-button
                >

                <p>
                  Category:
                  {{ currentSection.mats[mat].program?.competition_category?.name ?? "" }}
                </p>
                <p>Weight: {{ currentSection.mats[mat].program.weight_code }}</p>
                <template v-if="currentSection.mats[mat].bout">
                  <a-typography-title :level="3">
                    <span v-if="currentSection.mats[mat].bout.white == 0">---</span
                    ><span v-else>{{
                      currentSection.mats[mat].bout.white_player?.name_display
                    }}</span>
                    vs
                    <span v-if="currentSection.mats[mat].bout.white == 0">---</span
                    ><span v-else>{{
                      currentSection.mats[mat].bout.blue_player?.name_display
                    }}</span>
                  </a-typography-title>
                </template>
                <div>
                  <div class="flex flex-col">
                    <div class="overflow-x-auto">
                      <div class="inline-block min-w-full">
                        <div class="overflow-hidden">
                          <table class="min-w-full text-center text-sm font-light">
                            <thead
                              class="border-b bg-white font-medium dark:border-neutral-500 dark:bg-neutral-600"
                            >
                              <tr>
                                <th class="py-4 w-10">Program</th>
                                <th class="py-4 w-10">Sequence</th>
                                <th class="py-4 w-10">In Program</th>
                                <th class="py-4 w-10">Queue</th>
                                <th class="py-4 w-10">White</th>
                                <th class="py-4 w-10">Blue</th>
                              </tr>
                            </thead>
                            <tbody>
                              <template
                                v-for="bout in competition.bouts.filter(
                                  (x) => x.mat == mat && x.section == selectSection
                                )"
                                :key="bout.id"
                              >
                                <tr
                                  @click="selectBout(bout, mat, selectSection)"
                                  :class="
                                    select_bouts.includes(bout.id) ||
                                    select_bout.id == bout.id
                                      ? 'border border-blue-500'
                                      : ''
                                  "
                                  class="border-b dark:border-neutral-500 dark:bg-neutral-600 odd:bg-neutral-100"
                                >
                                  <td class="py-4 w-10">{{ bout.program_id }}</td>
                                  <td class="py-4 w-10">{{ bout.sequence }}</td>
                                  <td class="py-4 w-10">
                                    {{ bout.in_program_sequence }}
                                  </td>
                                  <td class="py-4 w-10">{{ bout.queue }}</td>
                                  <td class="py-4 w-10">
                                    {{ bout.white_player?.name_display }}
                                  </td>
                                  <td class="py-4 w-10">
                                    {{ bout.blue_player?.name_display }}
                                  </td>
                                </tr>
                              </template>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-200 p-4 grow w-32">
          <embed type="text/html" class="h-[87vh] w-full" :src="printLink" />
        </div>
      </div>
    </template>
    <template v-else>
      <div class="p-6">
        <a-card>
          <a-empty>
            <template #description>
              <h3 class="font-bold text-lg">no schedule</h3>
              <p>no schedule hint</p>
              <inertia-link
                :href="route('manage.competition.programs.index', competition.id)"
              >
                <a-button type="primary" class="bg-blue-500">
                  no schedule action
                </a-button>
              </inertia-link>
            </template>
          </a-empty>
        </a-card>
      </div>
    </template>
  </ProgramLayout>
</template>
<style>
.resizable {
  resize: horizontal; /* 可调整大小 */
  overflow: auto; /* 添加滚动条 */
  width: 70vw; /* 设置初始宽度 */
  height: 90vh; /* 设置初始高度 */
}
</style>
<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import { extractIdentifiers } from "@vue/compiler-core";

export default {
  components: {
    ProgramLayout,
  },
  props: ["competition"],
  data() {
    return {
      currentSection: {
        id: 1,
        mats: {},
      },
      selectSection: 1,
      printLink: "",
      select_bout: {},
      select_bouts: [],
      dateFormat: "YYYY-MM-DD",
      columns: [
        {
          title: "In program Sequence",
          dataIndex: "in_program_sequence",
        },
        {
          title: "Sequence",
          dataIndex: "sequence",
        },
        {
          title: "Queue",
          dataIndex: "queue",
        },
        {
          title: "Round",
          dataIndex: "round",
        },
        {
          title: "White",
          dataIndex: "white",
        },
        {
          title: "Blue",
          dataIndex: "blue",
        },
        {
          title: "White from",
          dataIndex: "white_rise_from",
        },
        {
          title: "Blue from",
          dataIndex: "blue_rise_from",
        },
      ],

      rules: {
        country: { required: true },
        name: { required: true },
        date_start: { required: true },
        date_end: { required: true },
      },
      validateMessages: {
        required: "${label} is required!",
        types: {
          email: "${label} is not a valid email!",
          number: "${label} is not a valid number!",
        },
        number: {
          range: "${label} must be between ${min} and ${max}",
        },
      },
    };
  },
  created() {
    for (var m = 1; m <= this.competition.mat_number; m++) {
      this.currentSection.mats[m] = {
        sequence: 1,
        program: {},
      };
    }
    this.onMatChange();
  },
  methods: {
    onSectionChange() {
      Object.entries(this.currentSection.mats).forEach(([matId, mat]) => {
        mat.bout = {};
        mat.program = {};
        mat.sequence = 1;
      });
      // this.onMatChange();
    },
    onMatChange() {
      Object.entries(this.currentSection.mats).forEach(([matId, mat]) => {
        if (mat.sequence < 1) {
          mat.sequence = 1;
          return true;
        }
        console.log(this.competition.programs_bouts);
        var tmpBout = this.competition.bouts.find(
          (b) =>
            b.section == this.currentSection.id &&
            b.mat == matId &&
            b.sequence == mat.sequence
        );
        if (tmpBout != undefined) {
          mat.bout = tmpBout;
          mat.program = this.competition.programs.find(
            (p) => p.id == mat.bout.program_id
          );
        } else {
          mat.sequence = this.competition.bouts.filter(
            (b) => b.section == this.currentSection.id && b.mat == matId
          ).length;
        }
      });
    },
    confirmPrint(competition) {
      console.log(this.printLink);
      this.printLink = route("manage.print.program_schedule", {
        competition_id: competition.id,
        bouts: this.select_bouts,
      });
    },
    selectBout(bout, mat, section) {
      console.log(mat, section);
      if (this.select_bouts.length != 0) {
        if (this.select_bout.mat == mat && this.select_bout.section == section) {
          if (bout.queue > this.select_bout.queue) {
            this.select_bouts = this.competition.bouts
              .filter((x) => x.mat == mat && x.section == section)
              .slice(this.select_bout.queue - 1, bout.queue)
              .map((x) => x.id);
          } else {
            this.select_bouts = this.competition.bouts
              .filter((x) => x.mat == mat && x.section == section)
              .slice(bout.queue - 1, this.select_bout.queue)
              .map((x) => x.id);
          }
        } else {
          console.log(3);
          this.select_bout = bout;
          this.select_bouts = [bout.id];
        }
      } else {
        console.log(4);
        this.select_bout = bout;
        this.select_bouts = [bout.id];
      }
      console.log(this.select_bouts);
    },
  },
  watch: {
    "currentSection.id"() {
      console.log(this.currentSection);
    },
  },
};
</script>
