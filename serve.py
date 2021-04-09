#!/usr/bin/env python

# WS server example that synchronizes state across clients

import asyncio
import json
import websockets
import random
import uuid
import hashlib
import base64

from log import get_logger


USERS = set()

DATADB = {}


def state_event():
    STATE = {}
    for user in DATADB.values():
        STATE[user['name']] = user['history']
    return json.dumps({"type": "state", "data": STATE})


def users_event():
    name = []
    for user in DATADB.values():
        name.append(user['name'])
    return json.dumps({"type": "users", "count": len(USERS), "name": name})


def getRandom():
    return random.randrange(1, 99)


def set_msg(msg):
    return json.dumps({'type': 'msg', 'message': str(msg)})


def new_point(point):
    return json.dumps({'type': 'newpoint', 'point': point})


def reg_user(user_uuid, name):
    return json.dumps({'type': 'reg', 'uuid': str(user_uuid), 'name': str(name)})

def send_message(websocket, message):
    return asyncio.wait([asyncio.create_task(websocket.send(message))])

def reset_all():
    for user in DATADB:
        DATADB[user]['history'] = []

async def rst_all():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = set_msg('已清除历史数据')
        reset_all()
        [await send_message(user,message) for user in USERS]
        await notify_state()

async def set_name(websocket, name):
    message = ''
    for user in DATADB.values():
        if name == user['name'] or not name:
            message = set_msg('昵称已存在')
    if not message:
        user_uuid = uuid.uuid1().hex[16:]
        if user_uuid in DATADB:
            DATADB[user_uuid]['name'] = name
        else:
            DATADB[user_uuid] = {'name': name, 'history': []}
            await notify_users()
        message = reg_user(user_uuid, name)
    await send_message(websocket, message)


async def need_random(websocket, user_uuid):
    if user_uuid and user_uuid in DATADB.keys() and DATADB[user_uuid]['name']:
        num = getRandom()
        DATADB[user_uuid]['history'].append(num)

        await send_message(websocket, new_point(num))

        await notify_state()
    else:
        await send_message(websocket, set_msg('请先设置昵称'))


async def notify_state():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = state_event()
        [await send_message(user,message) for user in USERS]


async def notify_users():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = users_event()
        [await send_message(user,message) for user in USERS]

async def register(websocket):
    USERS.add(websocket)
    user_uuid = uuid.uuid1().hex[16:]
    if user_uuid in DATADB.keys():
        message = reg_user(user_uuid, DATADB[user_uuid]['name'])
        await send_message(websocket, message)

    await notify_users()


async def unregister(websocket):
    USERS.remove(websocket)
    await notify_users()


async def counter(websocket, path):
    # register(websocket) sends user_event() to websocket
    await register(websocket)
    try:
        await websocket.send(state_event())
        async for data in websocket:

            data = json.loads(data)
            log.critical(data)
            if data['action'] == 'setname':
                await set_name(websocket, data['value'])
            elif data['action'] == 'random':
                await need_random(websocket, data['user_uuid'])
            elif data['action'] == 'clear_history':
                if data['pwd'] == 'xiang':
                    await rst_all()
            else:
                log.error("unsupported event: %s", data)
    finally:
        await unregister(websocket)

if __name__ == '__main__':
    log = get_logger(__name__, 'ERROR')
    start_server = websockets.serve(counter, "localhost", 6789)

    asyncio.get_event_loop().run_until_complete(start_server)
    log.critical('服务端启动成功')

    asyncio.get_event_loop().run_forever()
