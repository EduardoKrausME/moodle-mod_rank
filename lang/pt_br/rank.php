<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * rank.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['allowresubmit'] = 'Permitir que o estudante altere sua ordenação';
$string['allowresubmit_help'] = 'Quando habilitado, o estudante pode abrir novamente a atividade e alterar a ordem já enviada.';
$string['averageposition'] = 'Posição média';
$string['averagerank'] = 'Posição da turma';
$string['cannotchangeitemcount'] = 'A quantidade de itens não pode ser alterada depois que existem respostas. Você pode editar o texto dos itens mantendo a mesma quantidade.';
$string['draghelp'] = 'Arraste os itens para alterar a ordem. Pelo teclado, selecione um item e use Alt + seta para cima/baixo.';
$string['duplicateitems'] = 'Cada item deve ser único.';
$string['eventresponsecreated'] = 'Resposta de ordenação enviada';
$string['eventresponseupdated'] = 'Resposta de ordenação atualizada';
$string['instructions'] = 'Instruções';
$string['instructions_help'] = 'Explique qual critério o estudante deve usar para ordenar os itens, como prioridade, cronologia, importância ou sequência de um processo.';
$string['invaliditemcount'] = 'A atividade de ordenação deve conter entre 5 e 10 itens.';
$string['invalidranking'] = 'A ordenação enviada é inválida. Todos os itens devem aparecer exatamente uma vez.';
$string['item'] = 'Item';
$string['itemnumber'] = 'Item ';
$string['itemsgap'] = 'Não deixe campos vazios entre os itens. Os campos em branco devem ficar somente após o último item.';
$string['itemshelp'] = 'Informe entre 5 e 10 itens. Deixe em branco apenas os campos não utilizados no final da lista.';
$string['minimumitems'] = 'Informe pelo menos 5 itens.';
$string['modulename'] = 'Ordenação';
$string['modulenameplural'] = 'Ordenações';
$string['noresponses'] = 'Nenhuma resposta foi enviada ainda.';
$string['pluginadministration'] = 'Administração da ordenação';
$string['pluginname'] = 'Ordenação';
$string['privacy:export:response'] = 'Resposta de ordenação';
$string['privacy:metadata:rank_response_items'] = 'Armazena a posição de cada item em uma resposta de ordenação.';
$string['privacy:metadata:rank_response_items:itemid'] = 'Identificador do item ordenado.';
$string['privacy:metadata:rank_response_items:position'] = 'Posição atribuída ao item pelo usuário.';
$string['privacy:metadata:rank_response_items:responseid'] = 'Identificador da resposta.';
$string['privacy:metadata:rank_responses'] = 'Armazena a resposta enviada por cada usuário.';
$string['privacy:metadata:rank_responses:rankid'] = 'Identificador da atividade de ordenação.';
$string['privacy:metadata:rank_responses:timecreated'] = 'Momento em que a resposta foi enviada pela primeira vez.';
$string['privacy:metadata:rank_responses:timemodified'] = 'Momento da última alteração da resposta.';
$string['privacy:metadata:rank_responses:userid'] = 'Usuário que enviou a resposta.';
$string['rank:addinstance'] = 'Adicionar uma nova atividade de ordenação';
$string['rank:submit'] = 'Enviar uma ordenação';
$string['rank:view'] = 'Visualizar atividades de ordenação';
$string['rank:viewreport'] = 'Visualizar relatórios de ordenação';
$string['rankname'] = 'Nome da ordenação';
$string['ranksettings'] = 'Configurações da ordenação';
$string['report'] = 'Relatório de ordenação';
$string['reportfor'] = 'Relatório de ordenação: ';
$string['reportsummary'] = 'Respostas recebidas: ';
$string['responses'] = 'Respostas';
$string['responsesaved'] = 'Sua ordenação foi salva.';
$string['resubmitdisabled'] = 'Esta ordenação já foi enviada e não pode mais ser alterada.';
$string['submitranking'] = 'Enviar ordenação';
$string['updateranking'] = 'Atualizar ordenação';
$string['viewreport'] = 'Ver relatório da turma';
$string['yourranking'] = 'Sua ordenação atual';
