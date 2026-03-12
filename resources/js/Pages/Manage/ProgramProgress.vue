<template>
  <inertia-head title="比賽進度 - Dashboard" />

  <ProgramLayout :competition="competition">
    <!-- 頁面標題區域 -->
    <a-page-header 
      title="比賽進度" 
      subtitle="即時追蹤各場地比賽狀況"
      class="bg-white shadow-sm"
    >
      <template #extra>
        <div class="flex items-center gap-4">
          <!-- 場地數量控制 -->
          <div class="flex items-center gap-2 border rounded-lg px-3 py-1">
            <span class="text-gray-500">場地:</span>
            <a-input-number
              v-model:value="visibleMatsCount"
              :min="1"
              :max="competition?.mat_number || 1"
              @change="handleMatCountChange"
              class="w-16"
            />
            <span class="text-gray-400">/{{ competition?.mat_number || 1 }}</span>
          </div>

          <!-- 已選場次信息 -->
          <a-badge :count="selectedBouts?.length || 0" show-zero>
            <span class="text-gray-600 mr-2">已選場次</span>
          </a-badge>

          <!-- 操作按鈕 -->
          <a-space>
            <a-button 
              type="text" 
              danger
              :disabled="!selectedBouts?.length"
              @click="clearSelection"
            >
              <template #icon><delete-outlined /></template>
              清除選擇
            </a-button>
            <a-button 
              type="primary"
              :disabled="!selectedBouts?.length"
              @click="confirmPrint(competition)"
              class="bg-black"
            >
              <template #icon><printer-outlined /></template>
              顯示賽程
            </a-button>
          </a-space>
        </div>
      </template>
    </a-page-header>

    <!-- 主要內容區域 -->
    <template v-if="competition?.status >= 2">
      <div class="flex h-[calc(100vh-120px)]">
        <!-- 左側場地列表 -->
        <div 
          class="bg-white border-r flex flex-col resizable"
          :style="{ width: leftPanelWidth }"
        >
          <!-- 日期和時段選擇區域 -->
          <div class="border-b bg-white p-3 space-y-2">
            <!-- 日期選擇 -->
            <div v-if="competition?.days?.length" class="flex items-center gap-2">
              <calendar-outlined class="text-gray-400 text-sm" />
              <span class="text-md text-gray-600">日期:</span>
              <a-radio-group 
                v-model:value="selectedDate" 
                button-style="solid"
                @change="handleDateChange"
                size="small"
                class="compact-radio-group"
              >
                <a-radio-button 
                  v-for="(day, index) in competition.days" 
                  :key="index" 
                  :value="day"
                >
                  {{ formatDate(day) }}
                </a-radio-button>
              </a-radio-group>
            </div>

            <!-- 時段選擇 -->
            <div v-if="competition?.section_number > 0" class="flex items-center gap-2">
              <field-time-outlined class="text-gray-400 text-sm" />
              <span class="text-md text-gray-600">時段:</span>
              <div class="flex flex-wrap gap-1">
                <a-button
                  v-for="section in competition.section_number"
                  :key="section"
                  size="small"
                  :type="currentSection.id === section ? 'primary' : 'default'"
                  @click="currentSection.id = section; onSectionChange()"
                  class="text-md px-2 py-0 h-6"
                >
                  第{{ section }}時段
                </a-button>
              </div>
            </div>
          </div>

          <!-- 場地卡片網格 -->
          <div v-if="currentSection?.mats" class="flex-1 overflow-y-auto p-3">
            <div 
              class="grid gap-3"
              :style="{ 
                gridTemplateColumns: `repeat(${visibleMatsCount}, minmax(320px, 1fr))` 
              }"
            >
              <a-card
                v-for="mat in visibleMats" 
                :key="mat"
                :title="`場地 ${mat}`"
                :bordered="true"
                class="shadow-sm hover:shadow-md transition-shadow compact-card"
                :headStyle="{ backgroundColor: '#f0f5ff', padding: '0 12px', minHeight: '40px' }"
                :bodyStyle="{ padding: '12px' }"
              >
                <!-- 當前比賽信息 -->
                <div v-if="currentSection?.mats[mat]?.program" class="mb-2">
                  <a-space size="small" wrap>
                    <a-tag color="blue" class="text-md px-1 py-0">
                      {{ currentSection.mats[mat].program?.competition_category?.name || '未分類' }}
                    </a-tag>
                    <a-tag color="cyan" class="text-md px-1 py-0">
                      體重: {{ currentSection.mats[mat].bout.convertWeight || 'N/A' }}
                    </a-tag>
                  </a-space>
                </div>

                <!-- 當前對陣 -->
                <div class="mb-3 p-2 bg-gray-50 rounded-lg">
                  <div class="flex justify-between items-center mb-1">
                    <span class="text-md text-gray-500">
                      <field-time-outlined class="mr-1" />
                      當前 #{{ currentSection?.mats[mat]?.queue || 1 }}
                    </span>
                    <span v-if="currentSection?.mats[mat]?.bout?.scheduled_time" class="text-md text-gray-400">
                      {{ formatTime(currentSection.mats[mat].bout.scheduled_time) }}
                    </span>
                  </div>
                  <div v-if="currentSection?.mats[mat]?.bout" class="text-center">
                    <a-row :gutter="4" align="middle">
                      <a-col :span="10" class="text-right">
                        <div class="text-sm font-medium truncate" :title="currentSection.mats[mat].bout.white_player?.name_display">
                          <span class="">
                            {{ currentSection.mats[mat].bout.white_player?.name + currentSection.mats[mat].bout.white_player?.name_secondary || '---' }}
                          </span>
                        </div>
                        <div class="text-sm font-medium truncate">
                          <span class="">
                            {{ currentSection.mats[mat].bout.white_player?.team?.name || '---' }}
                          </span>
                        </div>
                      </a-col>
                      <a-col :span="4" class="text-center">
                        <a-tag color="red" class="font-bold text-md px-1">VS</a-tag>
                      </a-col>
                      <a-col :span="10" class="text-left">
                        <div class="text-sm font-medium truncate" :title="currentSection.mats[mat].bout.blue_player?.name_display">
                          <span class="text-red-600">
                            {{ currentSection.mats[mat].bout.blue_player?.name + currentSection.mats[mat].bout.blue_player?.name_secondary || '---' }}
                          </span>
                        </div>
                        <div class="text-sm font-medium truncate">
                          <span class="text-red-600">
                            {{ currentSection.mats[mat].bout.blue_player?.team?.name || '---' }}
                          </span>
                        </div>
                      </a-col>
                    </a-row>
                    
                    <!-- 顯示比分摘要 -->
                    <div v-if="currentSection?.mats[mat]?.bout?.result" class="mt-1 text-xs">
                      <a-tag :color="getResultColor(currentSection.mats[mat].bout.result)">
                        {{ getResultSummary(currentSection.mats[mat].bout.result) }}
                      </a-tag>
                    </div>
                  </div>
                  <div v-else class="text-center text-gray-400 py-2">
                    <a-empty :image="simpleImage" description="暫無比賽" :imageStyle="{ height: '30px' }" />
                  </div>
                </div>

                <!-- 場次列表 -->
                <div class="mt-2">
                  <div class="flex justify-between items-center mb-1">
                    <span class="text-md font-medium">
                      <unordered-list-outlined class="mr-1" />
                      場次列表
                    </span>
                    <span class="text-md text-gray-400">
                      {{ getBoutsForMat(mat)?.length || 0 }} 場
                    </span>
                  </div>
                  <a-table
                    :dataSource="getBoutsForMat(mat) || []"
                    :columns="compactBoutColumns"
                    :pagination="false"
                    size="small"
                    :rowClassName="(record) => {
                      const classes = [];
                      if (isBoutSelected(record?.id)) classes.push('bg-black');
                      if (dragState.draggedBout?.id === record?.id) classes.push('dragging');
                      return classes.join(' ');
                    }"
                    :customRow="(record) => ({
                      onClick: (event) => {
                        if (event.target.closest('.ant-btn-link') || event.target.closest('.drag-handle')) {
                          return;
                        }
                        selectBout(record, mat);
                      }
                    })"
                    :scroll="{ y: 500 }"
                    class="compact-table"
                    :rowKey="record => record.id"
                  >
                    <!-- 拖拽手柄列 -->
                    <template #headerCell="{ column }">
                      <template v-if="column.key === 'drag'">
                        <holder-outlined class="text-gray-400" />
                      </template>
                    </template>
                    
                    <template #bodyCell="{ column, record, index }">
                      <!-- 拖拽手柄列 -->
                      <template v-if="column.key === 'drag'">
                        <div 
                          class="drag-handle"
                          draggable="true"
                          @dragstart="handleDragStart($event, record, index, mat)"
                          @dragover.prevent
                          @dragenter.prevent
                          @drop="handleDrop($event, record, index, mat)"
                          @dragend="handleDragEnd"
                        >
                          <holder-outlined />
                        </div>
                      </template>
                      
                      <template v-else-if="column.key === 'players'">
                        <div class="text-md leading-tight">
                          <div class=" truncate max-w-[250px]" :title="record?.white_player?.name_display">
                            {{ formatPlayerName(record?.white_player) }}
                          </div>
                          <div class="text-red-600 truncate max-w-[250px]" :title="record?.blue_player?.name_display">
                            {{ formatPlayerName(record?.blue_player) }}
                          </div>
                        </div>
                      </template>
                      <template v-else-if="column.key === 'queue'">
                        <span class="text-md font-medium">#{{ record?.queue || '-' }}</span>
                      </template>
                      <template v-else-if="column.key === 'bout_name'">
                        <span class="text-md text-gray-600">{{ record.bout_name || '-' }}</span>
                      </template>
                      <template v-else-if="column.key === 'time'">
                        <span class="text-md text-gray-500">
                          {{ formatTime(record?.result?.time) }}
                        </span>
                      </template>
                      <template v-else-if="column.key === 'result'">
                        <div class="flex items-center gap-1">
                          <a-button 
                            size="small" 
                            type="link" 
                            @click.stop="showResultModal(record)"
                            class="text-md px-1"
                          >
                            {{ record?.result ? '修改' : '輸入' }}
                          </a-button>
                          <a-tag 
                            v-if="record?.result" 
                            :color="getResultColor(record.result)"
                            class="text-xs"
                          >
                            {{ getResultShort(record.result) }}
                          </a-tag>
                        </div>
                      </template>
                    </template>
                  </a-table>
                </div>
              </a-card>
            </div>
          </div>
        </div>

        <!-- 右側預覽區域 -->
        <div class="bg-gray-100 flex-1 flex flex-col">
          <div class="p-2 bg-white border-b flex justify-between items-center">
            <span class="text-sm font-medium">
              <file-text-outlined class="mr-1" />
              賽程預覽
            </span>
            <div class="flex items-center gap-2">
              <span v-if="selectedBouts?.length > 0" class="text-md text-gray-500">
                已選 {{ selectedBouts.length }} 場
              </span>
              <a-button 
                v-if="printLink" 
                size="small" 
                type="link" 
                @click="printLink = ''"
                class="text-md"
              >
                關閉
              </a-button>
            </div>
          </div>
          <div class="flex-1 p-2">
            <a-card v-if="printLink" :bodyStyle="{ padding: 0, height: '100%' }" class="h-full">
              <iframe 
                :src="printLink" 
                class="w-full h-full rounded"
                frameborder="0"
              />
            </a-card>
            <a-card v-else class="h-full flex items-center justify-center">
              <a-empty description="請選擇場次後點擊「顯示賽程」">
                <template #image>
                  <file-search-outlined style="font-size: 36px; color: #bfbfbf" />
                </template>
              </a-empty>
            </a-card>
          </div>
        </div>
      </div>
    </template>

    <!-- 無賽程狀態 -->
    <template v-else>
      <div class="p-8">
        <a-card>
          <a-result
            status="info"
            title="暫無賽程"
            sub-title="當前比賽尚未開始或沒有賽程安排"
          >
            <template #extra>
              <inertia-link :href="route('manage.competition.programs.index', competition?.id)">
                <a-button type="primary" class="bg-black">
                  前往設置賽程
                </a-button>
              </inertia-link>
            </template>
          </a-result>
        </a-card>
      </div>
    </template>

    <!-- 結果修改彈窗 - 加上時間選擇 -->
    <a-modal
      v-model:open="resultModalVisible"
      :title="`比賽結果 - ${currentEditingBout?.bout_name || ''}`"
      @ok="saveResult"
      @cancel="cancelResult"
      :destroyOnClose="true"
      :confirmLoading="savingResult"
      width="450px"
    >
      <a-form layout="vertical" size="middle">
        <!-- 使用時間選擇 -->
        <a-form-item label="使用時間">
          <a-time-picker
            v-model:value="usageTime"
            :format="'mm:ss'"
            :minuteStep="1"
            :secondStep="1"
            placeholder="選擇比賽使用時間"
            class="w-full"
            @change="handleTimeChange"
          />
          <div class="text-xs text-gray-400 mt-1">
            格式: 分鐘:秒鐘 (例如: 04:30)
          </div>
        </a-form-item>

        <!-- 計分區域 -->
        <div class="bg-gray-50 p-3 rounded-lg mb-3">
          <div class="flex justify-between items-center mb-2">
            <span class=" font-medium">白方</span>
            <span class="text-gray-400">VS</span>
            <span class="text-red-600 font-medium">藍方</span>
          </div>
          
          <!-- 分數輸入 -->
          <div class="grid grid-cols-2 gap-4">
            <!-- 白方分數 -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-500">一本</span>
                <a-input-number 
                  v-model:value="whiteIppon" 
                  :min="0" 
                  :max="99"
                  size="small"
                  class="w-16"
                />
              </div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-500">半勝</span>
                <a-input-number 
                  v-model:value="whiteWazari" 
                  :min="0" 
                  :max="99"
                  size="small"
                  class="w-16"
                />
              </div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-500">有效</span>
                <a-input-number 
                  v-model:value="whiteYuko" 
                  :min="0" 
                  :max="99"
                  size="small"
                  class="w-16"
                />
              </div>
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">指導</span>
                <a-input-number 
                  v-model:value="whiteShido" 
                  :min="0" 
                  :max="4"
                  size="small"
                  class="w-16"
                />
              </div>
            </div>
            
            <!-- 藍方分數 -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-500">一本</span>
                <a-input-number 
                  v-model:value="blueIppon" 
                  :min="0" 
                  :max="99"
                  size="small"
                />
              </div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-500">半勝</span>
                <a-input-number 
                  v-model:value="blueWazari" 
                  :min="0" 
                  :max="99"
                  size="small"
                  class="w-16"
                />
              </div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-500">有效</span>
                <a-input-number 
                  v-model:value="blueYuko" 
                  :min="0" 
                  :max="99"
                  size="small"
                  class="w-16"
                />
              </div>
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">指導</span>
                <a-input-number 
                  v-model:value="blueShido" 
                  :min="0" 
                  :max="4"
                  size="small"
                  class="w-16"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- 結果狀態選擇 -->
        <a-form-item label="比賽結果" required>
          <a-select 
            v-model:value="resultForm.status" 
            placeholder="請選擇比賽結果"
            size="middle"
          >
            <a-select-option :value="null">請選擇結果</a-select-option>
            
            <!-- 白方勝利 -->
            <a-select-option :value="10" class="">
              🏆 白方勝利 (10)
            </a-select-option>
            
            <!-- 紅方勝利 -->
            <a-select-option :value="11" class="text-red-600">
              🏆 紅方勝利 (11)
            </a-select-option>
            
            <!-- 白方退賽/棄權等，紅方勝利 -->
            <a-select-option :value="20">
              ⚠️ 白方退賽 - 紅方勝利 (20)
            </a-select-option>
            <a-select-option :value="30">
              🏥 白方傷病 - 紅方勝利 (30)
            </a-select-option>
            <a-select-option :value="40">
              🚫 白方犯規輸 - 紅方勝利 (40)
            </a-select-option>
            
            <!-- 藍方退賽/棄權等，白方勝利 -->
            <a-select-option :value="21">
              ⚠️ 藍方退賽 - 白方勝利 (21)
            </a-select-option>
            <a-select-option :value="31">
              🏥 藍方傷病 - 白方勝利 (31)
            </a-select-option>
            <a-select-option :value="41">
              🚫 藍方犯規輸 - 白方勝利 (41)
            </a-select-option>
            
            <!-- 取消 -->
            <a-select-option :value="-1" class="text-gray-400">
              ❌ 比賽取消 (-1)
            </a-select-option>
          </a-select>
        </a-form-item>
        
        <!-- 分數摘要 -->
        <div class="text-xs text-gray-500 bg-black p-2 rounded">
          <div class="flex justify-between">
            <span class="">白方: {{ whiteIppon }}/{{ whiteWazari }}/{{ whiteYuko }}/{{ whiteShido }}</span>
            <span>-</span>
            <span class="text-red-600">藍方: {{ blueIppon }}/{{ blueWazari }}/{{ blueYuko }}/{{ blueShido }}</span>
          </div>
          <div v-if="usageTimeSeconds > 0" class="text-center mt-1 text-gray-600">
            使用時間: {{ formatTimeFromSeconds(usageTimeSeconds) }}
          </div>
        </div>
      </a-form>
    </a-modal>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import { 
  DeleteOutlined, 
  PrinterOutlined, 
  MinusOutlined, 
  PlusOutlined,
  FileTextOutlined,
  FileSearchOutlined,
  CalendarOutlined,
  FieldTimeOutlined,
  UnorderedListOutlined,
  HolderOutlined
} from '@ant-design/icons-vue';
import { Empty, message, Modal } from 'ant-design-vue';
import dayjs from 'dayjs';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import { debounce } from 'lodash-es';

dayjs.extend(customParseFormat);

export default {
  components: {
    ProgramLayout,
    DeleteOutlined,
    PrinterOutlined,
    MinusOutlined,
    PlusOutlined,
    FileTextOutlined,
    FileSearchOutlined,
    CalendarOutlined,
    FieldTimeOutlined,
    UnorderedListOutlined,
    HolderOutlined,
  },
  props: {
    competition: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      currentSection: {
        id: 1,
        mats: {},
      },
      selectedDate: null,
      selectedBout: null,
      selectedBouts: [],
      printLink: "",
      leftPanelWidth: '70%',
      visibleMatsCount: 1,
      simpleImage: Empty.PRESENTED_IMAGE_SIMPLE,
      
      // 拖拽相關狀態
      dragState: {
        draggedBout: null,
        draggedIndex: -1,
        sourceMat: null,
        isDragging: false
      },
      
      // 壓縮版的表格列定義（添加拖拽列）
      compactBoutColumns: [
        {
          title: ' ',
          key: 'drag',
          width: 40,
          align: 'center',
        },
        {
          title: '#',
          key: 'queue',
          width: 45,
          align: 'center',
        },
        {
          title: '場次',
          key: 'bout_name',
          width: 65,
          align: 'center',
        },
        {
          title: '對陣',
          key: 'players',
        },
        {
          title: '時間',
          key: 'time',
          width: 70,
          align: 'center',
        },
        {
          title: '結果',
          key: 'result',
          width: 100,
          align: 'center',
        },
      ],
      
      // 快取數據
      cachedBouts: {},
      cachedPrograms: {},
      
      // 輪詢相關
      pollingInterval: null,
      lastUpdateTime: null,
      isLoading: false,
      
      // 結果修改相關
      resultModalVisible: false,
      savingResult: false,
      currentEditingBout: null,
      
      // 計分數據
      whiteIppon: 0,
      whiteWazari: 0,
      whiteYuko: 0,
      whiteShido: 0,
      blueIppon: 0,
      blueWazari: 0,
      blueYuko: 0,
      blueShido: 0,
      
      // 使用時間相關
      usageTime: null,
      usageTimeSeconds: 0,
      
      resultForm: {
        status: null,
        note: '',
      }
    };
  },
  computed: {
    // 計算當前顯示的場地
    visibleMats() {
      if (!this.competition?.mat_number) {
        return [];
      }
      return Array.from({ length: this.visibleMatsCount || 1 }, (_, i) => i + 1);
    },
    
    // 過濾後的場次
    filteredBouts() {
      if (!this.competition?.bouts) {
        return [];
      }
      
      const cacheKey = `${this.selectedDate}_${this.currentSection.id}`;
      if (this.cachedBouts[cacheKey]) {
        return this.cachedBouts[cacheKey];
      }
      
      let bouts = this.competition.bouts || [];
      
      // 按日期過濾
      if (this.selectedDate && bouts.length > 0) {
        bouts = bouts.filter(bout => {
          if (!bout?.date) return false;
          try {
            return dayjs(bout.date).format('YYYY-MM-DD') === this.selectedDate;
          } catch (e) {
            return false;
          }
        });
      }
      
      // 過濾掉 queue=0 的場次，並按 queue 排序
      bouts = bouts
        .filter(bout => bout?.queue && bout.queue > 0)
        .sort((a, b) => (a.queue || 0) - (b.queue || 0));
      
      // 存入快取
      this.cachedBouts[cacheKey] = bouts;
      
      return bouts;
    }
  },
  created() {
    this.$nextTick(() => {
      this.initializeComponent();
      this.startPolling();
    });
  },
  mounted() {
    this.loadUserPreferences();
  },
  beforeUnmount() {
    this.stopPolling();
  },
  methods: {
    // 初始化組件
    initializeComponent() {
      if (!this.competition) {
        console.warn('Competition data is not available');
        return;
      }
      
      // 預先快取 programs
      if (this.competition.programs) {
        this.competition.programs.forEach(program => {
          this.cachedPrograms[program.id] = program;
        });
      }
      
      // 設置默認顯示場地數量
      if (this.competition.mat_number) {
        this.visibleMatsCount = Math.min(3, this.competition.mat_number);
      }
      
      // 設置默認日期
      if (this.competition.days && this.competition.days.length > 0) {
        this.selectedDate = this.competition.days[0];
      }
      
      // 設置默認時段
      if (this.competition.section_number > 0) {
        this.currentSection.id = 1;
      }

      this.initializeMats();
    },
    
    // 初始化場地數據
    initializeMats() {
      if (!this.competition?.mat_number) {
        return;
      }
      
      const mats = {};
      for (let m = 1; m <= this.competition.mat_number; m++) {
        mats[m] = {
          queue: 1,
          program: {},
          bout: {}
        };
      }
      this.currentSection.mats = mats;
      
      this.onMatChange();
    },
    
    // 啟動輪詢
    startPolling() {
      this.pollingInterval = setInterval(() => {
        this.checkForUpdates();
      }, 30000);
    },
    
    // 停止輪詢
    stopPolling() {
      if (this.pollingInterval) {
        clearInterval(this.pollingInterval);
        this.pollingInterval = null;
      }
    },
    
    // 檢查更新
    checkForUpdates: debounce(function() {
      if (this.isLoading) return;
      
      this.isLoading = true;
      
      // 保存當前選擇的日期和時段
      const currentDate = this.selectedDate;
      const currentSectionId = this.currentSection.id;
      
      this.$inertia.reload({
        only: ['competition'],
        preserveState: true,
        preserveScroll: true,
        // 添加這兩個選項來保持日期和時段不變
        data: {
          // 可以將當前選擇的日期和時段作為參數發送給後端
          // 這樣後端可以根據這些參數返回相應的數據
          date: currentDate,
          section: currentSectionId
        },
        onSuccess: () => {
          this.isLoading = false;
          this.lastUpdateTime = Date.now();
          
          // 清除快取但保持選擇狀態
          this.cachedBouts = {};
          
          // 重新計算當前場次，但不要重置選擇
          this.onMatChange();
          this.selectedDate = currentDate,
          this.currentSection.id = currentSectionId,
          // 可選：更新後保持選擇的場次（如果需要）
          // 如果需要保持選擇的場次，可以在這裡重新驗證選擇的場次是否仍然有效
          
          message.success('數據已更新');
        },
        onError: () => {
          this.isLoading = false;
        }
      });
    }, 1000),
    
    // 清除快取
    clearCache() {
      this.cachedBouts = {};
    },
    
    // 加載用戶偏好
    loadUserPreferences() {
      try {
        const savedMatCount = localStorage.getItem('preferredMatCount');
        if (savedMatCount && this.competition?.mat_number) {
          this.visibleMatsCount = Math.min(
            parseInt(savedMatCount), 
            this.competition.mat_number
          );
        }
      } catch (e) {
        console.warn('Failed to load user preferences:', e);
      }
    },
    
    // 格式化球員名稱
    formatPlayerName(player) {
      if (!player) return '---';
      return `${player.name || ''}${player.name_secondary || ''}/${player.team?.name || ''}`;
    },
    
    // 格式化日期
    formatDate(date) {
      if (!date) return '';
      try {
        return dayjs(date).format('MM/DD');
      } catch (e) {
        return date;
      }
    },
    
    // 格式化時間
    formatTime(time) {
      if (!time && time !== 0) return '--:--';
      try {
        const minutes = Math.floor(time / 60);
        const seconds = Math.floor(time % 60);
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
      } catch (e) {
        return '--:--';
      }
    },
    
    // 日期變化處理
    handleDateChange: debounce(function() {
      this.clearCache();
      this.onMatChange();
      try {
        localStorage.setItem('preferredDate', this.selectedDate);
      } catch (e) {
        console.warn('Failed to save date preference:', e);
      }
    }, 300),
    
    // 獲取指定場地的場次
    getBoutsForMat(mat) {
      if (!this.filteredBouts?.length || !this.currentSection) {
        return [];
      }
      
      return this.filteredBouts.filter(
        x => x?.mat == mat && x?.section == this.currentSection.id
      );
    },
    
    // 場地數量變化
    handleMatCountChange() {
      try {
        localStorage.setItem('preferredMatCount', this.visibleMatsCount);
      } catch (e) {
        console.warn('Failed to save preference:', e);
      }
    },
    
    // 檢查場次是否被選中
    isBoutSelected(boutId) {
      return boutId && this.selectedBouts?.includes(boutId);
    },
    
    // 清除選擇
    clearSelection() {
      this.selectedBouts = [];
      this.selectedBout = null;
      message.success('已清除所有選擇');
    },
    
    // 選擇場次
    selectBout: debounce(function(bout, mat) {
      if (!bout?.id) return;
      
      if (!this.selectedBouts?.length) {
        this.selectedBout = bout;
        this.selectedBouts = [bout.id];
        return;
      }
      
      const firstSelectedBoutId = this.selectedBouts[0];
      const firstSelectedBout = this.findBoutById(firstSelectedBoutId);
      
      if (!firstSelectedBout) {
        this.selectedBout = bout;
        this.selectedBouts = [bout.id];
        return;
      }
      
      if (firstSelectedBout.mat !== bout.mat) {
        message.warning('只能選擇同一個場地的比賽');
        return;
      }
      
      if (firstSelectedBout.section !== bout.section) {
        message.warning('只能選擇同一個時段的比賽');
        return;
      }
      
      const boutsInCurrentMat = this.getBoutsForMat(mat);
      const startIndex = boutsInCurrentMat.findIndex(b => b.id === firstSelectedBoutId);
      const endIndex = boutsInCurrentMat.findIndex(b => b.id === bout.id);
      
      if (startIndex !== -1 && endIndex !== -1) {
        const min = Math.min(startIndex, endIndex);
        const max = Math.max(startIndex, endIndex);
        this.selectedBouts = boutsInCurrentMat.slice(min, max + 1).map(b => b.id);
        this.selectedBout = bout;
      }
    }, 100),
    
    // 根據ID查找場次
    findBoutById(boutId) {
      if (!boutId || !this.filteredBouts) return null;
      return this.filteredBouts.find(b => b.id === boutId);
    },
    
    // 時段切換
    onSectionChange() {
      if (!this.currentSection?.mats) return;
      
      this.clearCache();
      this.selectedBouts = [];
      this.selectedBout = null;
      
      Object.entries(this.currentSection.mats).forEach(([matId, mat]) => {
        if (mat) {
          mat.bout = {};
          mat.program = {};
          mat.queue = 1;
        }
      });
      this.onMatChange();
    },
    
    // 場次變化
    onMatChange() {
      if (!this.currentSection?.mats || !this.competition?.bouts) return;
      
      requestAnimationFrame(() => {
        Object.entries(this.currentSection.mats).forEach(([matId, mat]) => {
          if (!mat) return;
          
          const boutsInMat = this.getBoutsForMat(matId);
          
          if (boutsInMat?.length > 0) {
            const nextBout = boutsInMat.find(bout => {
              const isNotStarted = !bout.result || bout.result.status === 0;
              return isNotStarted;
            });
            
            if (nextBout) {
              mat.queue = nextBout.queue;
              mat.bout = nextBout;
              mat.program = this.cachedPrograms[nextBout?.program_id] || {};
            } else {
              const lastBout = boutsInMat[boutsInMat.length - 1];
              mat.queue = lastBout.queue;
              mat.bout = lastBout;
              mat.program = this.cachedPrograms[lastBout?.program_id] || {};
            }
          } else {
            mat.queue = 1;
            mat.bout = {};
            mat.program = {};
          }
        });
      });
    },
    
    // 拖拽開始
    handleDragStart(event, bout, index, mat) {
      if (!bout) return;
      
      this.dragState = {
        draggedBout: bout,
        draggedIndex: index,
        sourceMat: mat,
        isDragging: true
      };
      
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', JSON.stringify({
        id: bout.id,
        queue: bout.queue,
        mat: mat,
        index: index
      }));
      
      event.dataTransfer.effectAllowed = 'move';
    },
    
    // 拖拽結束
    handleDragEnd(event) {
      this.dragState.isDragging = false;
    },
    
    // 放置處理
    async handleDrop(event, targetBout, targetIndex, targetMat) {
      event.preventDefault();
      
      const dragDataStr = event.dataTransfer.getData('text/plain');
      if (!dragDataStr) return;
      
      try {
        const dragData = JSON.parse(dragDataStr);
        
        if (!dragData.id || !targetBout) return;
        
        if (dragData.mat !== targetMat) {
          message.warning('只能在同一場地內調整順序');
          return;
        }
        
        const draggedBout = this.findBoutById(dragData.id);
        if (!draggedBout || draggedBout.section !== targetBout.section) {
          message.warning('只能在同一時段內調整順序');
          return;
        }
        
        if (dragData.index === targetIndex) return;
        
        const boutsInMat = this.getBoutsForMat(targetMat);
        
        // 重新排序
        const newBouts = [...boutsInMat];
        const [movedBout] = newBouts.splice(dragData.index, 1);
        newBouts.splice(targetIndex, 0, movedBout);
        
        // 更新 queue 值
        const updates = newBouts.map((bout, idx) => ({
          id: bout.id,
          queue: idx + 1
        }));
        
        Modal.confirm({
          title: '確認調整順序',
          content: '確定要調整這些場次的順序嗎？',
          onOk: async () => {
            await this.updateBoutsQueue(updates);
          },
          onCancel: () => {
            this.checkForUpdates();
          }
        });
        
      } catch (error) {
        console.error('拖拽處理失敗:', error);
        message.error('調整順序失敗');
      }
    },
    
    // 批量更新場次順序
    async updateBoutsQueue(updates) {
      if (!updates || updates.length === 0) return;
      
      try {
        this.isLoading = true;
        
        await this.$inertia.post(route('manage.competition.bouts.update.queue', {
          competition: this.competition.id
        }), {
          updates: updates
        }, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            message.success('場次順序已更新');
            this.clearCache();
            this.onMatChange();
          },
          onError: (errors) => {
            console.error('更新順序失敗:', errors);
            message.error('更新順序失敗，請稍後再試');
            this.checkForUpdates();
          }
        });
      } catch (error) {
        console.error('更新順序時出錯:', error);
        message.error('更新順序失敗，請稍後再試');
      } finally {
        this.isLoading = false;
        this.dragState.isDragging = false;
      }
    },
    
    // 確認打印
    confirmPrint(competition) {
      if (!this.selectedBouts?.length) {
        message.warning('請先選擇場次');
        return;
      }
      this.printLink = route("manage.print.program_schedule", {
        competition_id: competition?.id,
        bouts: this.selectedBouts,
        date: this.selectedDate,
        section: this.currentSection.id
      });
    },
    
    // 獲取結果摘要
    getResultSummary(resultJson) {
      if (!resultJson) return '';
      try {
        const result = resultJson;
        const statusText = this.getStatusText(result.status);
        
        let timeText = '';
        if (result.time && result.time > 0) {
          timeText = ` ${this.formatTimeFromSeconds(result.time)}`;
        }
        return `${statusText}${timeText}`;
      } catch (e) {
        return '';
      }
    },
    
    // 獲取結果顏色
    getResultColor(resultJson) {
      if (!resultJson) return 'default';
      try {
        const result = resultJson;
        const status = result.status;
        if (status === 10 || status === 21 || status === 31 || status === 41) return 'blue';
        if (status === 11 || status === 20 || status === 30 || status === 40) return 'red';
        if (status === -1) return 'default';
        return 'default';
      } catch (e) {
        return 'default';
      }
    },
    
    // 獲取結果簡短顯示
    getResultShort(resultJson) {
      if (!resultJson) return '';
      try {
        return this.getStatusText(resultJson.status);
      } catch (e) {
        return '';
      }
    },
    
    // 獲取狀態文字
    getStatusText(status) {
      const statusMap = {
        '-1': '取消',
        '10': '白方勝',
        '11': '紅方勝',
        '20': '白退賽',
        '30': '白傷病',
        '40': '白犯規',
        '21': '藍退賽',
        '31': '藍傷病',
        '41': '藍犯規',
      };
      return statusMap[status] || '';
    },
    
    // 時間變化處理
    handleTimeChange(time) {
      if (time) {
        const minutes = time.minute();
        const seconds = time.second();
        this.usageTimeSeconds = (minutes * 60) + seconds;
      } else {
        this.usageTimeSeconds = 0;
      }
    },
    
    // 從秒數轉換為 dayjs 對象
    secondsToDayjs(seconds) {
      if (!seconds && seconds !== 0) return null;
      const minutes = Math.floor(seconds / 60);
      const remainingSeconds = seconds % 60;
      return dayjs().minute(minutes).second(remainingSeconds).millisecond(0);
    },
    
    // 格式化秒數為 mm:ss
    formatTimeFromSeconds(seconds) {
      if (!seconds && seconds !== 0) return '00:00';
      const mins = Math.floor(seconds / 60);
      const secs = seconds % 60;
      return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    },
    
    // 顯示結果修改彈窗
    showResultModal(bout) {
      this.currentEditingBout = bout;
      
      if (bout.result) {
        try {
          const result = bout.result;
          
          this.resultForm.status = result.status || null;
          this.resultForm.note = result.note || '';
          
          this.whiteIppon = result.w_ippon || 0;
          this.whiteWazari = result.w_wazari || 0;
          this.whiteYuko = result.w_yuko || 0;
          this.whiteShido = result.w_shido || 0;
          this.blueIppon = result.b_ippon || 0;
          this.blueWazari = result.b_wazari || 0;
          this.blueYuko = result.b_yuko || 0;
          this.blueShido = result.b_shido || 0;
          
          this.usageTimeSeconds = result.time || 0;
          this.usageTime = this.secondsToDayjs(this.usageTimeSeconds);
          
        } catch (e) {
          console.error('解析結果失敗:', e);
          this.resetForm();
        }
      } else {
        this.resetForm();
      }
      
      this.resultModalVisible = true;
    },
    
    // 重置表單
    resetForm() {
      this.resultForm = {
        status: null,
        note: '',
      };
      this.whiteIppon = 0;
      this.whiteWazari = 0;
      this.whiteYuko = 0;
      this.whiteShido = 0;
      this.blueIppon = 0;
      this.blueWazari = 0;
      this.blueYuko = 0;
      this.blueShido = 0;
      
      this.usageTime = null;
      this.usageTimeSeconds = 0;
    },
    
    // 取消結果修改
    cancelResult() {
      this.resultModalVisible = false;
      this.currentEditingBout = null;
      this.resetForm();
    },
    
    // 保存結果
    async saveResult() {
      if (!this.currentEditingBout) return;
      
      if (!this.resultForm.status && this.resultForm.status !== 0) {
        message.warning('請選擇比賽結果');
        return;
      }
      
      this.savingResult = true;
      
      try {
        const resultData = {
          status: this.resultForm.status,
          note: this.resultForm.note,
          w_ippon: this.whiteIppon,
          w_wazari: this.whiteWazari,
          w_yuko: this.whiteYuko,
          w_shido: this.whiteShido,
          b_ippon: this.blueIppon,
          b_wazari: this.blueWazari,
          b_yuko: this.blueYuko,
          b_shido: this.blueShido,
          time: this.usageTimeSeconds
        };
        
        await this.$inertia.post(route('manage.competition.bout.update.result', {
          competition: this.competition.id,
          bout: this.currentEditingBout.id
        }), resultData, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            message.success('比賽結果已更新');
            this.resultModalVisible = false;
            this.currentEditingBout = null;
            this.resetForm();
            this.checkForUpdates();
          },
          onError: (errors) => {
            console.error('保存失敗:', errors);
            message.error('保存失敗，請稍後再試');
          }
        });
      } catch (error) {
        console.error('保存結果時出錯:', error);
        message.error('保存失敗，請稍後再試');
      } finally {
        this.savingResult = false;
      }
    }
  },
  watch: {
  "currentSection.id"() {
    // 只有當用戶手動切換時段時才清除選擇
    // 如果是數據刷新導致的變化，不應該清除選擇
    if (!this.isLoading) {
      this.clearCache();
      this.selectedBouts = [];
      this.selectedBout = null;
      this.onSectionChange();
    }
  },
  selectedDate() {
    // 只有當用戶手動切換日期時才清除選擇
    if (!this.isLoading) {
      this.clearCache();
      this.selectedBouts = [];
      this.selectedBout = null;
      this.onMatChange();
    }
  },
    competition: {
      handler(newVal) {
        if (newVal) {
          this.clearCache();
          this.$nextTick(() => {
            this.initializeComponent();
          });
        }
      },
      immediate: true,
      deep: true
    }
  },
};
</script>

<style scoped>
.resizable {
  resize: horizontal;
  overflow: auto;
  min-width: 500px;
  max-width: 85%;
}

/* 壓縮卡片樣式 */
.compact-card :deep(.ant-card-head) {
  padding: 0 12px;
  min-height: 36px;
}

.compact-card :deep(.ant-card-head-title) {
  padding: 8px 0;
  font-size: 14px;
}

.compact-card :deep(.ant-card-extra) {
  padding: 6px 0;
}

/* 壓縮表格樣式 */
.compact-table :deep(.ant-table) {
  font-size: 12px;
}

.compact-table :deep(.ant-table-thead > tr > th) {
  padding: 4px 4px;
  font-size: 11px;
  font-weight: 600;
  background-color: #fafafa;
}

.compact-table :deep(.ant-table-tbody > tr > td) {
  padding: 4px 4px;
  line-height: 1.3;
}

.compact-table :deep(.ant-table-row) {
  cursor: pointer;
}

.compact-table :deep(.ant-table-row:hover) td {
  background-color: #e6f7ff !important;
}

/* 拖拽手柄樣式 */
.drag-handle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  transition: all 0.2s;
  cursor: grab;
  user-select: none;
  color: #bfbfbf;
}

.drag-handle:hover {
  color: #1890ff;
  background-color: #f0f0f0;
}

.drag-handle:active {
  cursor: grabbing;
  background-color: #e6f7ff;
}

/* 拖拽中的行樣式 */
:deep(tr.dragging) {
  opacity: 0.5;
  background-color: #e6f7ff !important;
  border: 2px dashed #1890ff !important;
}

/* 壓縮日期選擇按鈕組 */
:deep(.compact-radio-group) .ant-radio-button-wrapper {
  padding: 0 8px;
  line-height: 24px;
  height: 24px;
  font-size: 12px;
}

/* 背景色 */
:deep(.bg-black) {
  background-color: #e6f7ff;
}

/* 自定義滾動條 */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* 按鈕組樣式 */
:deep(.ant-btn-sm) {
  font-size: 12px;
}

/* 輸入框壓縮 */
:deep(.ant-input-number-sm) {
  width: 40px;
}

:deep(.ant-input-number-sm input) {
  height: 22px;
  font-size: 12px;
  text-align: center;
}

/* 標籤壓縮 */
:deep(.ant-tag) {
  margin-right: 4px;
  font-size: 11px;
  line-height: 18px;
  padding: 0 4px;
}

/* TimePicker 樣式 */
:deep(.ant-time-picker) {
  width: 100%;
}

:deep(.ant-time-picker-input) {
  font-size: 12px;
  height: 28px;
}

:deep(.ant-time-picker-panel) {
  font-size: 12px;
}

/* 結果彈窗樣式 */
:deep(.ant-select) {
  width: 100%;
}

:deep(.) {
  color: #2563eb;
}

:deep(.text-red-600) {
  color: #dc2626;
}

:deep(.bg-black) {
  background-color: #eff6ff;
}
</style>