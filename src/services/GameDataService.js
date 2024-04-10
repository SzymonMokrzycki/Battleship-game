import api from "./api";

class GameDataService {
    async getAllShips(){
        return api.get('/game/get-all-ships');
    }

    async getAllIslands(){
        return api.get('/game/get-all-islands');
    }

    async getHistoryBattles(user_id, page){
        return api.get('/game/user-battles/'+user_id+'?page='+page);
    }

    async getUserQueries(user_id){
        return api.get(`/game/user-queries/${user_id}`);
    }

    async getUserFleets(user_id){
        return api.get(`/game/user-fleets/${user_id}`);
    }

    async getFleetShips(fleet_id){
        return api.get(`/game/fleet-ships/${fleet_id}`);
    }

    async getWorldUsers(world_id){
        return api.get(`/game/world-played-users/${world_id}`);
    }

    async getAllItems(){
        return api.get('/game/all-items');
    }

    async getFleetShips(fleet_id){
        return api.get(`/game/fleet-ships/${fleet_id}`);
    }

    async addQueryGame(queryData){
        return api.post('/game/all-queries', {
            sender_id: queryData.sender_id,
            sender: queryData.sender,
            status: "waiting",
            user_id: queryData.user_id,
            sender_fleet: queryData.sender_fleet
        });
    }

    async acceptQuery(id){
        return api.put(`/game/all-queries/${id}`, {
            status: "accepted"
        })
    }

    async rejectQuery(id){
        return api.put(`/game/all-queries/${id}`, {
            status: "rejected"
        })
    }

    async addFleet(fleetData){
        return api.post(`/game/all-fleets`, {
            name: fleetData.name,
            number_of_ships: fleetData.number_of_ships,
            user_id: fleetData.user_id
        })
    }

    async getUserFleets(user_id){
        return api.get(`/game/user-fleets/${user_id}`)
    }

    async buyShip(ship_id, fleet_id){
        return api.put(`/game/update-ship/${ship_id}`, {
            fleet_id: fleet_id
        });
    }

    async getShip(ship_id){
        return api.get(`/game/all-ships/${ship_id}`)
    }

    async getItem(item_id){
        return api.get(`/game/all-items/${item_id}`)
    }

    async getQueryStatus(query_id){
        return api.get(`/game/all-queries/${query_id}`);
    }

    async getQuery(query_id){
        return api.get(`/game/all-queries/${query_id}`);
    }

    async addNewGame(id, sender_fleet_id, recipient_fleet_id){
        return api.post(`/game/online-game-data/${id}`, {
            sender_fleet_id: sender_fleet_id,
            recipient_fleet_id: recipient_fleet_id
        });
    }

    async getGameData(id){
        return api.get(`/game/get-data-game/${id}`)
    }

    async getGame(id){
        return api.get(`/game/get-data-game/${id}`)
    }

    async getEquipmentItems(id){
        return api.get(`/game/equipment-items/${id}`)
    }

    async updateSenderPositions(id, senderPositions){
        return api.put(`/game/store-positions/${id}`, {
            sender_positions: senderPositions
        })
    }

    async updateRecipientPositions(id, recipientPositions){
        return api.put(`/game/store-positions/${id}`, {
            recipient_positions: recipientPositions
        })
    }

    async updateMoving(id, dataMoving){
        return api.put(`/game/store-moving/${id}`, {
            user_name: dataMoving.user_name,
            ships: dataMoving.ships,
            current_moving: dataMoving.current_moving,
            last_move: dataMoving.last_move,
            round: dataMoving.round
        })
    }

    async updateGameStatus(id){
        return api.put(`/game/update-game-status/${id}`);
    }

    async gameOver(id, finishedData){
        return api.put(`/game/finish-game/${id}`, {
            result: finishedData.result
        });
    }

    async saveHistory(historyData){
        return api.post(`/game/all-battles`, {
            result: historyData.result,
            number_lost_ships: historyData.number_lost_ships,
            experience: historyData.experience,
            battle_points: historyData.battle_points,
            user_id: historyData.user_id,
            fleet_id: historyData.fleet_id
        });
    }

    async addEquipment(equipmentData){
        return api.post(`/game/all-equipments`, {
            number_of_items: equipmentData.number_of_items,
            user_id: equipmentData.user_id
        })
    }

    async buyItem(item_id, equipment_id){
        return api.put(`/game/all-items/${item_id}`, {
            equipment_id: equipment_id
        })
    }

    async updateEquipment(equipment_id, numberItems){
        return api.put(`/game/all-equipments/${equipment_id}`, {
            number_of_items: numberItems
        })
    }

    async updateShip(ship_id, updateData){
        return api.put(`/game/ship-use-item/${ship_id}`, {
            hp: updateData.hp,
            armament: updateData.armament,
            strong_crew: updateData.strongcrew,
            number_of_canons: updateData.number_canons,
            damage_canons: updateData.damage_canons
        })
    }

    async deletItemEquipment(item_id, equipment_id){
        return api.put(`/game/delete-item-equipment/${item_id}`, {
            equipment_id: equipment_id
        })
    }
}

export default new GameDataService();