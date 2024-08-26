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
                            <td>
                                <a href="/manage/print/tournament_quarter?size=4" target="_blank">4 Quarter</a>&nbsp;
                                <a href="/manage/print/tournament_quarter?size=4&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="/manage/print/tournament_knockout?size=4" target="_blank">4 Knockout</a>&nbsp;
                                <a href="/manage/print/tournament_knockout?size=4&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                        </tr>
                        <tr>
                            <th>8</th>
                            <td>
                                <a href="/manage/print/tournament_quarter?size=8" target="_blank">8 Quarter&nbsp;
                                <a href="/manage/print/tournament_quarter?size=8&winner_line=true" target="_blank">(with winner line)</a>
                            </a>
                            </td>
                            <td>
                                <a href="/manage/print/tournament_double?size=8" target="_blank">8 Double</a>&nbsp;
                                <a href="/manage/print/tournament_double?size=8&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                            <td></td>
                            <td>
                                <a href="/manage/print/tournament_knockout?size=8" target="_blank">8 Knockout</a>&nbsp;
                                <a href="/manage/print/tournament_knockout?size=8&winner_line=true" target="_blank">(with winner line)</a>
                            
                            </td>
                        </tr>
                        <tr>
                            <th>16</th>
                            <td>
                                <a href="/manage/print/tournament_quarter?size=16" target="_blank">16 Quarter</a>&nbsp;
                                <a href="/manage/print/tournament_quarter?size=16&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                            <td>
                                <a href="/manage/print/tournament_double?size=16" target="_blank">16 Double</a>&nbsp;
                                <a href="/manage/print/tournament_double?size=16&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                            <td>
                                <a href="/manage/print/tournament_full?size=16" target="_blank">16 Full</a>&nbsp;
                            </td>
                            <td>
                                <a href="/manage/print/tournament_knockout?size=16" target="_blank">16 Knockout</a>&nbsp;
                                <a href="/manage/print/tournament_knockout?size=16&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                        </tr>
                        <tr>
                            <th>32</th>
                            <td>
                                <a href="/manage/print/tournament_quarter?size=32" target="_blank">32 Quarter</a>&nbsp;
                                <a href="/manage/print/tournament_quarter?size=32&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                            <td>
                                <a href="/manage/print/tournament_double?size=32" target="_blank">32 Double</a>&nbsp;
                                <a href="/manage/print/tournament_double?size=32&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                            <td>
                                <a href="/manage/print/tournament_full?size=32" target="_blank">32 Full</a>&nbsp;
                            </td>
                            <td>
                                <a href="/manage/print/tournament_knockout?size=32" target="_blank">32 Knockout</a>&nbsp;
                                <a href="/manage/print/tournament_knockout?size=32&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                        </tr>
                        <tr>
                            <th>64</th>
                            <td>
                                <a href="/manage/print/tournament_quarter?size=64" target="_blank">64 Quarter</a>&nbsp;
                                <a href="/manage/print/tournament_quarter?size=64&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                            <td>
                                <a href="/manage/print/tournament_double?size=64" target="_blank">64 Double</a>&nbsp;
                                <a href="/manage/print/tournament_double?size=64&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
                            <td></td>
                            <td>
                                <a href="/manage/print/tournament_knockout?size=64" target="_blank">64 Knockout</a>&nbsp;
                                <a href="/manage/print/tournament_knockout?size=64&winner_line=true" target="_blank">(with winner line)</a>
                            </td>
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
                        <tr><td width="50">Size</td><td>Players (option 1)</td><td>Players (option 2)</td></tr>
                        <tr>
                            <th>3</th>
                            <td>*3 Players</td>
                            <td><a href="/manage/print/round_robbin_option2?size=3" target="_blank">3 Players</a></td>
                        </tr>
                        <tr>
                            <th>4</th>
                            <td>*4 Players</td>
                            <td><a href="/manage/print/round_robbin_option2?size=4" target="_blank">4 Players</a></td>
                        </tr>
                        <tr>
                            <th>5</th>
                            <td>*5 Players</td>
                            <td><a href="/manage/print/round_robbin_option2?size=5" target="_blank">5 Players</a></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="pt-6">
            <div >
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Other Print Out</div>
                    <a href="/manage/print/winners?gender=men" target="_blank">Winners (Men)</a><br>
                    <a href="/manage/print/winners?gender=women" target="_blank">Winners (Women)</a><br>
                    * Referee List<br>
                    * Contest Sheet<br>
                    * Contest Order Tatami X<br>
                    * Weight-in List<br>
                    * Deligation List<br>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
