# -*- coding: utf-8 -*-

# WS server example that synchronizes state across clients

import asyncio
import json
import websockets
import random
import time
import uuid

from log import get_logger


DATADB = {}

HEARTTIME = 60000


def state_event():
    STATE = []
    for user in DATADB.values():
        if check_heart(user['time']) and user['name']:
            for time in user['history']:

                STATE.append([time,user['name'], user['history'][time]])
    STATE = sorted(STATE, key=lambda k: k[0],reverse=True)
    return json.dumps({"type": "state", "data": STATE})


def users_event():
    name = []
    count = 0
    for user in DATADB.values():
        if check_heart(user['time']):
            count += 1
            name.append(user['name'])
    return json.dumps({"type": "users", "count": count, "name": name})


def getRandom(dford):
    count = 0
    detail = []
    # try:
    for d in dford:
        d_type = dford[d]
        rolltype = int(d[d.index('d')+1:])
        for i in range(0, int(d_type)):
            if d == '1d10':
                point = random.randrange(0, 10)
            elif d == '1d100':
                point = random.randrange(0, 100, 10)
            else:
                point = random.randrange(1, rolltype + 1)
            count += point
            detail.append([str(d),point])
    return (count, detail)

    # finally:
    #     print(detail)
    #     return count


def set_msg(msg):
    return json.dumps({'type': 'msg', 'message': str(msg)})


def new_point(point):
    return json.dumps({'type': 'newpoint', 'point': point})


def other_point(name, point):
    return json.dumps({'type': 'otherpoint', 'point': point, 'name': name})


def reg_user(name):
    return json.dumps({'type': 'reg', 'name': str(name)})


def send_message(user, message):
    if check_heart(user['time']):
        return asyncio.wait([asyncio.create_task(user['ws'].send(message))])
    else:
        return asyncio.sleep(0)


def reset_all():
    for user in DATADB:
        DATADB[user]['history'] = {}


def set_heartuuid():
    HEARTID = str(uuid.uuid4())
    return json.dumps({'type': 'checkid', 'name': HEARTID})


def check_heart(user_time):
    return int(round(time.time() * 1000)) - user_time < HEARTTIME


def set_heart(user_id, time):
    if user_id in DATADB:
        DATADB[user_id]['time'] = int(time)


async def rst_all():
    if DATADB:  # asyncio.wait doesn't accept an empty list
        message = set_msg('已清除历史数据')
        reset_all()
        [await send_message(DATADB[user], message) for user in DATADB]


async def set_name(name, user_uuid):
    message = ''
    for user in DATADB.values():
        if name == user['name'] or not name:
            message = set_msg('昵称已存在')
    if not message:
        if user_uuid in DATADB:
            DATADB[user_uuid]['name'] = name
        await notify_users()
        message = reg_user(name)
    await send_message(DATADB[user_uuid], message)


async def join_room(websocket, user_uuid):
    if user_uuid in DATADB:
        name = DATADB[user_uuid]['name']
        DATADB[user_uuid]['ws'] = websocket
        DATADB[user_uuid]['time'] = int(round(time.time() * 1000))
        message = reg_user(name)
        await send_message(DATADB[user_uuid], message)
    else:
        DATADB[user_uuid] = {'ws': websocket, 'name': '',
                             'history': {}, 'time': int(round(time.time() * 1000))}


async def need_random(user_uuid, dford, dtype):
    if user_uuid and user_uuid in DATADB.keys() and DATADB[user_uuid]['name']:
        num, detail = getRandom(dford)
        await send_message(DATADB[user_uuid], new_point(num))
        if not dtype:

            DATADB[user_uuid]['history'][int(round(time.time() * 1000))] = [
                num, detail]
            name = DATADB[user_uuid]['name']
            message = other_point(name, num)
            for user in DATADB:
                if user == user_uuid:
                    continue
                else:
                    await send_message(DATADB[user], message)
    else:
        await send_message(DATADB[user_uuid], set_msg('请先设置昵称'))


async def notify_state():
    if DATADB:  # asyncio.wait doesn't accept an empty list
        message = state_event()
        [await send_message(DATADB[user], message) for user in DATADB]


async def notify_users():
    if DATADB:  # asyncio.wait doesn't accept an empty list
        message = users_event()
        [await send_message(DATADB[user], message) for user in DATADB]

# async def unregister(websocket):
#     DATADB.remove(websocket)
#     await notify_users()


async def counter(websocket, path):
    # register(websocket) sends user_event() to websocket
    # await register(websocket)

    await websocket.send(state_event())
    async for data in websocket:
        data = json.loads(data)
        log.critical(data)
        if data['action'] == 'setname':
            await set_name(data['value'], data['user_uuid'])
        elif data['action'] == 'random':
            await need_random(data['user_uuid'], data['data'], data['dark'])
        elif data['action'] == 'clear_history':
            if data['pwd'] == 'xiang':
                await rst_all()
        elif data['action'] == 'join_room':
            await join_room(websocket, data['user_uuid'])
        elif data['action'] == 'set_heart':
            set_heart(data['user_uuid'], data['time'])
        else:
            log.error("unsupported event: %s", data)

        await notify_users()
        await notify_state()


if __name__ == '__main__':
    log = get_logger(__name__, 'ERROR')
    start_server = websockets.serve(counter, "0.0.0.0", 6789)

    asyncio.get_event_loop().run_until_complete(start_server)
    log.critical('服务端启动成功')

    asyncio.get_event_loop().run_forever()
