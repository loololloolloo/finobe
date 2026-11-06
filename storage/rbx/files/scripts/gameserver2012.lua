-- Finobe 2012 game server
--  - run by the 2012 client itself (there's no 2012 RCC): RobloxApp.exe -script <gameserver2012.ashx URL>,
--    started by tools/gameserver2012/host2012.ps1 on the game server machine
--  - the site fills in the {placeholders} and signs it (Game/GameServer2012.ashx)
--  - talks to the same /api/gameserver endpoints as the 2016 script (register, alive, verify, visit, update, shutdown)

local placeId = {placeId}
local port = {port}
local jobId = "{jobId}"
local creatorId = {creatorId}
local url = "{baseUrl}"
local accessKey = "{accessKey}"
local auth = "?accesskey=" .. accessKey

local Players = game:GetService("Players")

local function get(path)
	return game:HttpGet(url .. path .. auth, true)
end

-- the 2012 engine has no HttpService, so the player list JSON is built by hand
local function reportPlayers()
	pcall(function()
		local ids = {}
		for _, player in pairs(Players:GetChildren()) do
			if player:IsA("Player") then
				table.insert(ids, tostring(player.userId))
			end
		end
		local body = '{"jobId":"' .. jobId .. '","players":[' .. table.concat(ids, ",") .. ']}'
		game:HttpPost(url .. "/api/gameserver/update" .. auth, body, true)
	end)
end

pcall(function() settings().Network.UseInstancePacketCache = true end)
pcall(function() settings().Network.UsePhysicsPacketCache = true end)
pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.AccumulatedError end)
pcall(function() settings().Network.WaitingForCharacterLogRate = 100 end)
pcall(function() settings().Diagnostics:LegacyScriptMode() end)
pcall(function() settings().Diagnostics.LuaRamLimit = 0 end)

local scriptContext = game:GetService("ScriptContext")
scriptContext.ScriptsDisabled = true

pcall(function() game:SetPlaceID(placeId, false) end)
pcall(function() game:SetCreatorID(creatorId, Enum.CreatorType.User) end)
pcall(function() game:GetService("ChangeHistoryService"):SetEnabled(false) end)

pcall(function() game:GetService("ContentProvider"):SetBaseUrl(url .. "/") end)
pcall(function() game:GetService("ScriptInformationProvider"):SetAssetUrl(url .. "/Asset/") end)
pcall(function() game:GetService("ScriptInformationProvider"):SetAccessKey(accessKey) end)
pcall(function()
	local badges = game:GetService("BadgeService")
	badges:SetPlaceId(placeId)
	badges:SetAwardBadgeUrl(url .. "/Game/Badge/AwardBadge.ashx?UserID=%d&BadgeID=%d&PlaceID=%d&accesskey=" .. accessKey)
	badges:SetHasBadgeUrl(url .. "/Game/Badge/HasBadge.ashx?UserID=%d&BadgeID=%d")
	badges:SetIsBadgeDisabledUrl(url .. "/Game/Badge/IsBadgeDisabled.ashx?BadgeID=%d&PlaceID=%d")
	badges:SetIsBadgeLegalUrl(url .. "/Game/Badge/IsBadgeLegal.ashx?BadgeID=%d&PlaceID=%d")
end)
pcall(function()
	local insert = game:GetService("InsertService")
	insert:SetBaseSetsUrl(url .. "/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
	insert:SetUserSetsUrl(url .. "/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
	insert:SetCollectionUrl(url .. "/Game/Tools/InsertAsset.ashx?sid=%d")
	insert:SetAssetUrl(url .. "/Asset/?id=%d")
	insert:SetAssetVersionUrl(url .. "/Asset/?assetversionid=%d")
end)

local everyoneLeftAt = nil

Players.PlayerAdded:connect(function(player)
	print("Player " .. player.userId .. " added")
	everyoneLeftAt = nil
	-- the site only says yes for a user it just handed this server's join script to
	local ok, allowed = pcall(function() return get("/api/gameserver/verify/" .. jobId .. "/" .. player.userId) end)
	if ok and allowed ~= "1" then
		print("Player " .. player.userId .. " couldn't be verified, removing")
		pcall(function() player:Remove() end)
		return
	end
	pcall(function() get("/api/gameserver/visit/" .. jobId) end)
	reportPlayers()
end)

Players.PlayerRemoving:connect(function(player)
	print("Player " .. player.userId .. " leaving")
	delay(1, function()
		reportPlayers()
		if Players.NumPlayers == 0 then everyoneLeftAt = tick() end
	end)
end)

game:Load(url .. "/asset/?id=" .. placeId .. "&accesskey=" .. accessKey)

game:GetService("NetworkServer"):Start(port)

pcall(function() scriptContext:SetTimeout(10) end)
scriptContext.ScriptsDisabled = false

game:GetService("RunService"):Run()

local closed = false
local function shutdown()
	if closed then return end
	closed = true
	-- the site also tells host2012 to end this process
	pcall(function() get("/api/gameserver/shutdown/" .. jobId) end)
end
pcall(function() game.OnClose = shutdown end)

-- heartbeat; an empty server closes 3 minutes after starting, or 60s after the last player left
delay(0, function()
	local startedAt = tick()
	while wait(30) do
		pcall(function() get("/api/gameserver/alive/" .. jobId) end)
		if Players.NumPlayers == 0 then
			local idleSince = everyoneLeftAt or startedAt
			local grace = everyoneLeftAt and 60 or 180
			if tick() - idleSince > grace then
				shutdown()
				break
			end
		end
	end
end)

-- players polling PlaceLauncher get the join script from now on
get("/api/gameserver/register/" .. jobId)
print("Finobe 2012 server " .. jobId .. " running place " .. placeId .. " on port " .. port)
