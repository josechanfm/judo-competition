<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <div class="mx-4 py-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            </div>
        </template>

        <div class="pt-6">
            <div >
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Tournament Game</div>
                    <table width="100%" border="1">
                        <tr><td width="50">Size</td><td>Quater</td><td>Double</td><td>Full</td><td>Knockout</td></tr>
                        <tr>
                            <th>4</th>
                            <td><a href="/manage/print/tournament_quarter?size=4" target="_blank">4 Quarter</a></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>8</th>
                            <td><a href="/manage/print/tournament_quarter?size=8" target="_blank">8 Quarter</a></td>
                            <td><a href="/manage/print/tournament_double?size=8" target="_blank">8 Double</a></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>16</th>
                            <td><a href="/manage/print/tournament_quarter?size=16" target="_blank">16 Quarter</a></td>
                            <td><a href="/manage/print/tournament_double?size=16" target="_blank">16 Double</a></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>32</th>
                            <td><a href="/manage/print/tournament_quarter?size=32" target="_blank">32 Quarter</a></td>
                            <td><a href="/manage/print/tournament_double?size=32" target="_blank">32 Double</a></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>64</th>
                            <td><a href="/manage/print/tournament_quarter?size=64" target="_blank">64 Quarter</a></td>
                            <td><a href="/manage/print/tournament_double?size=64" target="_blank">64 Double</a></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="pt-6">
            <div >
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Round Robbin Game</div>
                    <table width="100%" border="1">
                        <tr><td width="50">Size</td><td>Players</td></tr>
                        <tr>
                            <th>3</th>
                            <td><a href="/manage/print/round_robbin?size=3" target="_blank">3 Players</a></td>
                        </tr>
                        <tr>
                            <th>4</th>
                            <td><a href="/manage/print/round_robbin?size=4" target="_blank">4 Players</a></td>
                        </tr>
                        <tr>
                            <th>5</th>
                            <td><a href="/manage/print/round_robbin?size=5" target="_blank">5 Players</a></td>
                        </tr>
                    </table>
                </div>
                    <a href="/manage/print/winners">Winners</a>
            </div>
        </div>
        <div class="pt-6">
            <div >
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Other Print Out</div>
                    <a href="/manage/print/winners">Winners</a>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
