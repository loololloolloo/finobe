local placeId, port, url, creatorid, accessKey = ...
-- the site authenticates game server callbacks with this key (FINOBE_CLOUD_AUTHORIZATION)
local auth = "?accesskey=" .. tostring(accessKey or "")
-- also makes RCC send the key as an "accesskey" header on every request it makes
pcall(function() game:GetService("ScriptInformationProvider"):SetAccessKey(tostring(accessKey or "")) end)
------------------- UTILITY FUNCTIONS --------------------------

local Players = game:GetService("Players")
local HttpService = game:GetService("HttpService")

function waitForChild(parent, childName)
	while true do
		local child = parent:findFirstChild(childName)
		if child then
			return child
		end
		parent.ChildAdded:wait()
	end
end

function reportPlayers()
    local success, message = pcall(function()
        local PlayerList = {}
        for _, player in pairs(Players:GetChildren()) do
            if player:IsA("Player") then
                table.insert(PlayerList, player.UserId)
            end
        end
        local MessagePayload = HttpService:JSONEncode({
            ["jobId"] = game.JobId,
            ["players"] = PlayerList
        })
        local ResponseData = game:HttpPost(url.."/api/gameserver/update" .. auth, MessagePayload, true, "application/json")
    end)

    if not success then
        warn("Error reporting players: ".. tostring(message))
    end
end	

-----------------------------------END UTILITY FUNCTIONS -------------------------

-----------------------------------"CUSTOM" SHARED CODE----------------------------------

pcall(function() settings().Network.UseInstancePacketCache = true end)
pcall(function() settings().Network.UsePhysicsPacketCache = true end)
pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.AccumulatedError end)

settings().Network.PhysicsSend = Enum.PhysicsSendMethod.TopNErrors
settings().Network.ExperimentalPhysicsEnabled = true
settings().Network.WaitingForCharacterLogRate = 100
pcall(function() settings().Diagnostics:LegacyScriptMode() end)

-----------------------------------START GAME SHARED SCRIPT------------------------------

local assetId = placeId -- might be able to remove this now

local scriptContext = game:GetService('ScriptContext')
pcall(function() scriptContext:AddStarterScript(37801172) end)
scriptContext.ScriptsDisabled = true

game:SetPlaceID(assetId, false)
game:SetUniverseId(assetId)
game:SetCreatorID(creatorid, Enum.CreatorType.User)
game:GetService("ChangeHistoryService"):SetEnabled(false)

-- establish this peer as the Server
local ns = game:GetService("NetworkServer")

if url~=nil then
	pcall(function() game:GetService("Players"):SetAbuseReportUrl(url .. "/AbuseReport/InGameChatHandler.ashx") end)
	pcall(function() game:GetService("ScriptInformationProvider"):SetAssetUrl(url .. "/Asset/") end)
	pcall(function() game:GetService("ContentProvider"):SetBaseUrl(url .. "/") end)
	-- pcall(function() game:GetService("Players"):SetChatFilterUrl(url .. "/Game/ChatFilter.ashx") end)

	game:GetService("BadgeService"):SetPlaceId(placeId)
    game:GetService("Players"):SetSysStatsUrl(url.. "/api/gameserver/sysstats-report?placeId=".. placeId)

	game:GetService("BadgeService"):SetAwardBadgeUrl(url .. "/Game/Badge/AwardBadge.ashx?UserID=%d&BadgeID=%d&PlaceID=%d&accesskey=" .. tostring(accessKey or ""))
	game:GetService("BadgeService"):SetHasBadgeUrl(url .. "/Game/Badge/HasBadge.ashx?UserID=%d&BadgeID=%d")
	game:GetService("BadgeService"):SetIsBadgeDisabledUrl(url .. "/Game/Badge/IsBadgeDisabled.ashx?BadgeID=%d&PlaceID=%d")
	game:GetService("BadgeService"):SetIsBadgeLegalUrl(url .. "/Game/Badge/IsBadgeLegal.ashx?BadgeID=%d&PlaceID=%d")
	game:GetService("InsertService"):SetBaseSetsUrl(url .. "/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
	game:GetService("InsertService"):SetUserSetsUrl(url .. "/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
	game:GetService("InsertService"):SetCollectionUrl(url .. "/Game/Tools/InsertAsset.ashx?sid=%d")
	game:GetService("InsertService"):SetAssetUrl(url .. "/Asset/?id=%d")
	game:GetService("InsertService"):SetAssetVersionUrl(url .. "/Asset/?assetversionid=%d")
	
	pcall(function() loadfile(url .. "/Game/LoadPlaceInfo.ashx?PlaceId=" .. placeId)() end)
end

-- client tickets are not checked by RCC (players are verified with the site in PlayerAdded instead)
pcall(function() game:GetService("NetworkServer"):SetIsPlayerAuthenticationRequired(false) end)
settings().Diagnostics.LuaRamLimit = 0


local everyoneLeftAt = nil

game:GetService("Players").PlayerAdded:connect(function(player)
	print("Player " .. player.userId .. " added")
	everyoneLeftAt = nil
	-- the site only says yes for a user it just handed this server's join script to (stops spoofed user ids)
	local ok, allowed = pcall(function()
		return game:HttpGet(url .. "/api/gameserver/verify/" .. game.JobId .. "/" .. player.userId .. auth, true)
	end)
	if ok and allowed ~= "1" then
		pcall(function() player:Kick("Couldn't verify you, rejoin from the website.") end)
		return
	end
	pcall(function() game:HttpGet(url .. "/api/gameserver/visit/" .. game.JobId .. auth) end)
	reportPlayers()

	-- movement animations picked on the Character page: lines of "<config> <Animation> <url>",
	-- applied to the character's Animate script every spawn (empty = keep the default ones)
	local animLines = ""
	pcall(function() animLines = game:HttpGet(url .. "/api/gameserver/animations/" .. player.userId .. auth, true) end)
	local function applyAnimations(character)
		if animLines == nil or animLines == "" then return end
		local animate = nil
		for i = 1, 50 do
			animate = character:FindFirstChild("Animate")
			if animate ~= nil or character.Parent == nil then break end
			wait(0.1)
		end
		if animate == nil then return end
		for config, child, id in string.gmatch(animLines, "(%S+) (%S+) (%S+)") do
			pcall(function()
				local value = animate:FindFirstChild(config)
				local anim = value and value:FindFirstChild(child)
				if anim ~= nil then anim.AnimationId = id end
			end)
		end
	end
	player.CharacterAdded:connect(function(character) spawn(function() applyAnimations(character) end) end)
	if player.Character ~= nil then spawn(function() applyAnimations(player.Character) end) end

	player.Chatted:connect(
		function(msg)
			if player.Character ~= nil then
				if msg == ";ec" or msg == ";energycell" or msg == ";bleach" or msg == ";suicide" or msg == ";cut" or msg == ";finobe" or msg == ";kms" or msg == ";raymonf" or msg == ";kyle" then
				local aeaea = Instance.new("Sound")
				aeaea.Parent = player.Character.Torso
				local higuw = math.random(1, 2)
				if higuw == 1 then
					aeaea.SoundId = url .. "/assets/?id=15"
				elseif higuw == 2 then
					aeaea.SoundId = url .. "/assets/?id=16"
				end
				aeaea.Volume = 0.7
				aeaea:Play()
				wait(0.1)
				player.Character.Humanoid.Health = 0
			end
		end
	end)
end)

game:GetService("Players").PlayerRemoving:connect(function(player)
	print("Player " .. player.userId .. " leaving")
	-- PlayerRemoving fires before the player is gone, so report once it's out of the list
	delay(1, function()
		reportPlayers()
		if Players.NumPlayers == 0 then everyoneLeftAt = tick() end
	end)
end)

if placeId~=nil and url~=nil then
	wait()
	
	game:Load(url .. "/asset/?id=" .. placeId .. "&accesskey=" .. tostring(accessKey or ""))
end

ns:Start(port) 

scriptContext:SetTimeout(10)
scriptContext.ScriptsDisabled = false

------------------------------END START GAME SHARED SCRIPT--------------------------

-- StartGame -- 
game:GetService("RunService"):Run()

local function shutdown()
	pcall(function() game:HttpGet(url .. "/api/gameserver/shutdown/" .. game.JobId .. auth) end)
end
game.OnClose = shutdown

-- the heartbeat runs in its own thread so this script returns and the site's OpenJobEx call finishes
spawn(function()
	local startedAt = tick()
	while wait(30) do
		pcall(function() game:HttpGet(url .. "/api/gameserver/alive/" .. game.JobId .. auth) end)
		if Players.NumPlayers == 0 then
			-- empty: give the first player 3 minutes to load in, and close 60s after the last one left
			local idleSince = everyoneLeftAt or startedAt
			local grace = everyoneLeftAt and 60 or 180
			if tick() - idleSince > grace then
				shutdown()
				break
			end
		end
	end
end)

-- tells the site the server is up: players polling PlaceLauncher get the join script from now on
game:HttpGet(url .. "/api/gameserver/register/" .. game.JobId .. auth)
